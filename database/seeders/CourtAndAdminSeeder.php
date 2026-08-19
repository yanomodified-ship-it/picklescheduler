<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CourtAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@homecourt.com'],
            [
                'name' => 'Admin Manager',
                'password' => Hash::make('admin12345'), // Change password in production
            ]
        );

        // 2. Ensure exactly Courts 1 through 7 exist
        for ($i = 1; $i <= 7; $i++) {
            $type =$i <= 4 ? 'Outdoor' : 'Indoor';
            Court::updateOrCreate(
                ['id' => $i],
                [
                    'name' => "Court {$i}",
                    'description' => "Professional {$type} pickleball court.",
                    'status' => 'active',
                    'price_per_hour' => 500.00,
                    'operating_hours_start' => '06:00:00',
                    'operating_hours_end' => '22:00:00',
                ]
            );
        }
    }
}