<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GownStock;
use App\Models\GownCollection;
use Inertia\Inertia;
use Illuminate\Http\Request;

class GownController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:admin');
    // }

    public function index()
    {
        return Inertia::render('admin/Gown/Index', [
            'stock' => GownStock::all(),
            'collections' => GownCollection::with('user')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/Gown/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'size' => 'required|in:XS,S,M,L,XL',
            'total' => 'required|integer|min:1',
        ]);

        GownStock::create([
            'size' => $request->size,
            'total' => $request->total,
            'issued' => 0,
            'available' => $request->total, // fix here
        ]);
        return redirect()->route('admin.gowns.index')->with('message', 'Gown stock added!');
    }

    public function edit(GownStock $gown)
    {
        return Inertia::render('admin/Gown/Edit', [
            'gown' => $gown,
        ]);
    }

    public function update(Request $request, GownStock $gown)
    {
        $request->validate([
            'size'  => 'required|in:XS,S,M,L,XL',
            'total' => 'required|integer|min:1',
        ]);

        $newTotal = (int) $request->total;
        $issued   = (int) $gown->issued;
        $available = max(0, $newTotal - $issued);

        $gown->update([
            'size'      => $request->size,
            'total'     => $newTotal,
            'available' => $available,
        ]);

        return redirect()->route('admin.gowns.index')->with('message', 'Gown stock updated!');
    }

    public function destroy(GownStock $gown)
    {
        $gown->delete();

        return redirect()->route('admin.gowns.index')->with('message', 'Gown stock deleted!');
    }
}
