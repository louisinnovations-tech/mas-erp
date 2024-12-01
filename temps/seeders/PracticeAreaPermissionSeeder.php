<?php

namespace Database\Seeders;

use App\Models\PracticeArea;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PracticeAreaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['name' => 'manage practice areas', 'guard_name' => 'web'],
            ['name' => 'create practice areas', 'guard_name' => 'web'],
            ['name' => 'edit practice areas', 'guard_name' => 'web'],
            ['name' => 'delete practice areas', 'guard_name' => 'web'],
        ];

        Permission::insert($items);

        $companyPermissions =  [
            ['name' => 'manage practice areas'],
            ['name' => 'create practice areas'],
            ['name' => 'edit practice areas'],
            ['name' => 'delete practice areas']
        ];

        $companyRole = Role::updateOrCreate(
            [
                'name' => 'company',
            ],
            [
                'created_by' => 0
            ]
        );

        $companyRole->givePermissionTo($companyPermissions);
    }
}
