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
        $manager = User::updateOrCreate(
            ['email' => 'manager@semre.com'],
            [
                'name'              => 'Manager User',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->syncRoles($managerRole);

        // Sales officer user
        $salesOfficer = User::updateOrCreate(
            ['email' => 'sales@semre.com'],
            [
                'name'              => 'Sales Officer',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $salesOfficer->syncRoles($salesOfficerRole);
    }
}
