<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure base roles exist
        $this->call(RoleSeeder::class);

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Vendor user
        $vendorUser = User::firstOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'name' => 'Demo Vendor',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if (! $vendorUser->hasRole('vendor')) {
            $vendorUser->assignRole('vendor');
        }

        // Ensure vendor profile exists and approved
        Vendor::firstOrCreate(
            ['user_id' => $vendorUser->id],
            [
                'company_name' => 'Acme Supplies',
                'gst_number' => null,
                'address' => 'Demo Address',
                'status' => 'approved',
            ]
        );
    }
}
