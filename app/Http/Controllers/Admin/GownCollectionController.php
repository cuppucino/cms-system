<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\GownCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\GownStock;

class GownCollectionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:admin');
    // }

    public function index()
    {
        $collections = GownCollection::with('user')->get();

        return Inertia::render('admin/Gown/GownCollection', [
            'collections' => $collections,
        ]);
    }

    public function update(Request $request, GownCollection $gownCollection)
    {
        $request->validate([
            'status' => 'required|in:reserved,collected,returned,late',
        ]);

        $newStatus = $request->string('status');

        return DB::transaction(function () use ($gownCollection, $newStatus) {
            // Lock the collection row
            $collection = GownCollection::where('id', $gownCollection->id)->lockForUpdate()->firstOrFail();
            $oldStatus  = $collection->status;

            // --- Validate transition ---
            $valid = [
                'reserved'  => ['collected'],          // collect only
                'collected' => ['returned', 'late'],    // return or mark late
                'late'      => ['returned'],           // only return from late
                'returned'  => [],                     // terminal
            ];
            if (!in_array($newStatus, $valid[$oldStatus] ?? [], true)) {
                return back()->withErrors(['status' => "Invalid status change: {$oldStatus} → {$newStatus}"]);
            }

            // Lock stock row
            $stock = GownStock::where('size', $collection->size)->lockForUpdate()->first();

            // --- Stock math ---
            // Reservation already decreased `available`.
            // - reserved -> collected: issued++
            // - collected/late -> returned: available++, issued--
            if ($stock) {
                if ($oldStatus === 'reserved' && $newStatus === 'collected') {
                    $stock->increment('issued');
                }

                if (in_array($oldStatus, ['collected', 'late'], true) && $newStatus === 'returned') {
                    $stock->increment('available');
                    if ($stock->issued > 0) {
                        $stock->decrement('issued');
                    }
                }
            }

            // --- Update collection record ---
            $collection->update([
                'status'          => $newStatus,
                'collection_date' => $newStatus === 'collected' ? now() : $collection->collection_date,
                'return_date'     => $newStatus === 'returned'  ? now() : $collection->return_date,
            ]);

            return redirect()->back()->with('message', 'Gown collection updated successfully!');
        });
    }
}
