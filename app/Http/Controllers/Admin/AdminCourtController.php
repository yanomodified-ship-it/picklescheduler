<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Http\Request;

class AdminCourtController extends Controller
{
    public function index()
    {
        $courts = Court::latest()->get();
        return view('admin.courts.index', compact('courts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|in:available,maintenance,closed',
        ]);

        Court::create($validated);

        return redirect()->route('admin.courts.index')
                         ->with('success', 'Court added successfully.');
    }

    public function edit(Court $court)
    {
        return view('admin.courts.edit', compact('court'));
    }

    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|in:available,maintenance,closed',
        ]);

        $court->update($validated);

        return redirect()->route('admin.courts.index')
                         ->with('success', 'Court updated successfully.');
    }

    public function destroy(Court $court)
    {
        $court->delete();

        return redirect()->route('admin.courts.index')
                         ->with('success', 'Court deleted successfully.');
    }
}