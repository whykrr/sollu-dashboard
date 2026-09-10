<?php

namespace Database\Seeders\Production;

use App\Enums\PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        /*
        * Create Permissions
        */
        foreach (PermissionEnum::cases() as $permission) {
            Permission::findOrCreate(
                $permission->value,
                'business'
            );
        }

    }
}
