<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

class AdminCourtController extends Controller
{
    // Handle the creation of a new court
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'status'                => 'required|in:active,maintenance,disabled',
            'classification'        => 'required|in:Indoor,Outdoor',
            'operating_hours_start' => 'required',
            'operating_hours_end'   => 'required',
        ]);

        Court::create($validated);

        return back()->with('success', 'New court added successfully.');
    }

    // Handle updates to existing courts
    public function update(Request $request, Court $court)
    {
        $validated = $request->validate([
            'status'                => 'required|in:active,maintenance,disabled',
            'classification'        => 'required|in:Indoor,Outdoor',
            'operating_hours_start' => 'required',
            'operating_hours_end'   => 'required',
        ]);

        $court->update($validated);

        return back()->with('success', "{$court->name} updated successfully.");
    }

    // Handle permanent deletion of a court
    public function destroy(Court $court)
    {
        $courtName = $court->name;
        
        // Optional: Check if the court has bookings before deleting
        // if ($court->bookings()->exists()) {
        //     return back()->with('error', "Cannot delete {$courtName} because it has existing bookings.");
        // }

        $court->delete();

        return back()->with('success', "{$courtName} has been completely removed.");
    }
}