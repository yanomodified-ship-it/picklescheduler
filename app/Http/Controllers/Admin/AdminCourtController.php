<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

class AdminCourtController extends Controller
{
    public function update(Request $request, Court$court)
    {
        $validated =$request->validate([
            'status'                => 'required|in:active,maintenance,disabled',
            'price_per_hour'        => 'required|numeric|min:0',
            'operating_hours_start' => 'required',
            'operating_hours_end'   => 'required',
        ]);

        $court->update($validated);

        return back()->with('success', "{$court->name} updated successfully.");
    }
}