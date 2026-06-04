<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Default seed: admin account only.
     * For full demo data, run: php artisan db:seed --class=DemoDatabaseSeeder
     */
    public function run(): void
    {
        $this->call(DefaultSeeder::class);
    }
}
