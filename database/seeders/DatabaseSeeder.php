<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $managerRole      = Role::firstOrCreate(['name' => 'manager']);
        $salesOfficerRole = Role::firstOrCreate(['name' => 'sales_officer']);

        // Manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@semre.com'],
            [
                'name'     => 'Manager User',
                'password' => Hash::make('password'),
            ]
        );
        $manager->assignRole($managerRole);

        // Sales officer user
        $salesOfficer = User::firstOrCreate(
            ['email' => 'sales@semre.com'],
            [
                'name'     => 'Sales Officer',
                'password' => Hash::make('password'),
            ]
        );
        $salesOfficer->assignRole($salesOfficerRole);
    }
}
