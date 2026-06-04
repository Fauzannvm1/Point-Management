<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultSeeder extends Seeder
{
    /**
     * Minimal seed: one admin account for production or blank-slate setups.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Admin User',
            'username' => 'admin',
            'email'    => 'admin@school.edu',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);
    }
}
