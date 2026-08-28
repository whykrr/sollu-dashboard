<?php

namespace Database\Seeders\Production;

use App\Models\CockpitUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CockpitUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CockpitUser::updateOrCreate(
            ['email' => 'admin@sollu.id'],
            [
                'name' => 'Master Cockpit Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
    }
}
