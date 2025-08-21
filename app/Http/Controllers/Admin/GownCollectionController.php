<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\GownCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GownCollectionController extends Controller
{
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

        $oldStatus = $gownCollection->status;
        $newStatus = $request->status;

        // Update gown collection record
        $gownCollection->update([
            'status' => $newStatus,
            'collection_date' => $newStatus === 'collected' ? now() : $gownCollection->collection_date,
            'return_date' => $newStatus === 'returned' ? now() : $gownCollection->return_date,
        ]);

        // Adjust gown stock
        $stock = \App\Models\GownStock::where('size', $gownCollection->size)->first();

        if ($stock) {
            if ($oldStatus !== 'collected' && $newStatus === 'collected') {
                // Student just collected → reduce available, increase issued
                $stock->decrement('available');
                $stock->increment('issued');
            }

            if ($oldStatus === 'collected' && $newStatus === 'returned') {
                // Student returned → increase available, reduce issued
                $stock->increment('available');
                $stock->decrement('issued');
            }
        }

        return redirect()->back()->with('message', 'Gown collection updated successfully!');
    }
}
