<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Court;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $courts = [
            ['name' => 'Court 1', 'status' => 'active'],
            ['name' => 'Court 2', 'status' => 'active'],
            ['name' => 'Court 3', 'status' => 'active'],
            ['name' => 'Court 4', 'status' => 'active'],
            ['name' => 'Court 5', 'status' => 'active'],
            ['name' => 'Court 6', 'status' => 'active'],
            ['name' => 'Court 7', 'status' => 'active'],
        ];

        foreach ($courts as $court) {
            Court::firstOrCreate(['name' => $court['name']], $court);
        }
    }
}