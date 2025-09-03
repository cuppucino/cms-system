<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GownCollection;
use App\Models\GownStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GownController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return Inertia::render('student/Gown', [
            'stock' => GownStock::orderBy('size')->get(['id', 'size', 'total', 'issued', 'available']),
            'collection' => GownCollection::where('user_id', $user->id)->first(),
        ]);
    }

    /**
     * Reserve a gown size (first time).
     * Locks one unit by decreasing `available`. Status becomes "reserved".
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'size' => 'required|in:XS,S,M,L,XL',
        ]);

        return DB::transaction(function () use ($user, $data) {
            // If already has a collection row -> reject (use update instead)
            $existing = GownCollection::where('user_id', $user->id)->first();
            if ($existing) {
                return redirect()->route('student.gown.show')
                    ->with('message', 'You already have a reservation. Use Change Size instead.');
            }

            $stock = GownStock::where('size', $data['size'])->lockForUpdate()->first();
            if (!$stock || $stock->available < 1) {
                return back()->with('message', 'Selected size is out of stock.');
            }

            // Lock one piece
            $stock->decrement('available');

            GownCollection::create([
                'user_id' => $user->id,
                'size' => $data['size'],
                'status' => 'reserved',
            ]);

            return redirect()->route('student.gown.show')->with('message', 'Gown reserved successfully.');
        });
    }

    /**
     * Change reserved size (only if not collected yet).
     * Puts back previous size and takes one from the new size.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'size' => 'required|in:XS,S,M,L,XL',
        ]);
        $collection = \App\Models\GownCollection::where('user_id', $request->user()->id)->lockForUpdate()->first();
        if ($collection) $this->authorize('update', $collection);

        return DB::transaction(function () use ($user, $data) {
            $collection = GownCollection::where('user_id', $user->id)->lockForUpdate()->first();

            if (!$collection) {
                return redirect()->route('student.gown.show')->with('message', 'No reservation found.');
            }
            if ($collection->status === 'collected') {
                return redirect()->route('student.gown.show')->with('message', 'You cannot change size after collection.');
            }
            if ($collection->size === $data['size']) {
                return redirect()->route('student.gown.show')->with('message', 'You already reserved this size.');
            }


            // return previous size
            $prev = GownStock::where('size', $collection->size)->lockForUpdate()->first();
            if ($prev) {
                $prev->increment('available');
            }

            // take new size
            $next = GownStock::where('size', $data['size'])->lockForUpdate()->first();
            if (!$next || $next->available < 1) {
                // revert previous increment safely
                if ($prev && $prev->available > 0) {
                    $prev->decrement('available');
                }
                return back()->with('message', 'New size is out of stock.');
            }
            $next->decrement('available');

            $collection->update(['size' => $data['size']]);

            return redirect()->route('student.gown.show')->with('message', 'Gown size updated.');
        });
    }

    /**
     * Cancel reservation (only if not collected yet).
     * Returns the unit back to `available`.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        return DB::transaction(function () use ($user) {
            $collection = GownCollection::where('user_id', $user->id)->lockForUpdate()->first();

            if (!$collection) {
                return redirect()->route('student.gown.show')->with('message', 'No reservation to cancel.');
            }
            if ($collection->status === 'collected') {
                return redirect()->route('student.gown.show')->with('message', 'You cannot cancel after collection.');
            }

            $stock = GownStock::where('size', $collection->size)->lockForUpdate()->first();
            if ($stock) $stock->increment('available');

            $collection->delete();

            return redirect()->route('student.gown.show')->with('message', 'Reservation cancelled.');
        });
    }
}
