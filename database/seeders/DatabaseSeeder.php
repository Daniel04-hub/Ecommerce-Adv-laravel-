<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Ensure roles exist
        $this->call(RoleSeeder::class);

        // 2) Sync existing users.role into Spatie roles
        $this->call(SyncUserRolesSeeder::class);
    }
}
