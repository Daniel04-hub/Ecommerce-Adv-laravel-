<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SyncUserRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Map existing users.role to Spatie roles, non-destructively
        $validRoles = [
            'admin', 'vendor', 'warehouse', 'finance', 'logistics', 'customer'
        ];

        // Ensure roles exist (idempotent)
        foreach ($validRoles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        User::query()->chunkById(200, function ($users) use ($validRoles) {
            foreach ($users as $user) {
                $role = $user->role; // existing column
                if ($role && in_array($role, $validRoles, true)) {
                    // Assign role if not already assigned
                    if (! $user->hasRole($role)) {
                        $user->assignRole($role);
                    }
                }
            }
        });
    }
}
