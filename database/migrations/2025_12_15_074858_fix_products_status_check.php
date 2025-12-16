<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove old constraint (if exists)
        DB::statement("
            ALTER TABLE products
            DROP CONSTRAINT IF EXISTS products_status_check
        ");

        // Add correct constraint
        DB::statement("
            ALTER TABLE products
            ADD CONSTRAINT products_status_check
            CHECK (status IN ('pending', 'active', 'inactive'))
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE products
            DROP CONSTRAINT IF EXISTS products_status_check
        ");
    }
};
