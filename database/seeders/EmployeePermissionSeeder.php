<?php

namespace Database\Seeders;

use App\Models\PracticeArea;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['name' => 'manage employee', 'guard_name' => 'web'],
            ['name' => 'create employee', 'guard_name' => 'web'],
            ['name' => 'edit employee', 'guard_name' => 'web'],
            ['name' => 'delete employee', 'guard_name' => 'web'],
        ];

        Permission::insert($items);

        $companyPermissions =  [
            ['name' => 'manage employee'],
            ['name' => 'create employee'],
            ['name' => 'edit employee'],
            ['name' => 'delete employee']
        ];

        $company = User::where('type','company')->first();
        $companyRole = Role::updateOrCreate(
            [
                'name' => 'company',
            ],
            [
                'created_by' => $company ? $company->id : 0
            ]
        );

        $companyRole->givePermissionTo($companyPermissions);

        $employeeRole = Role::updateOrCreate(
            [
                'name' => 'employee',
            ],
            [
                'created_by' => $company ? $company->id : 0
            ]
        );

        $employeePermissions = [
            ["name" => "show dashboard"],

            ["name" => "show group"],
            ["name" => "manage group"],

            ["name" => "manage case"],
            ["name" => "view case"],

            ["name" => "create todo"],
            ["name" => "edit todo"],
            ["name" => "view todo"],
            ["name" => "delete todo"],
            ["name" => "manage todo"],

            ["name" => "manage bill"],
            ["name" => "create bill"],
            ["name" => "edit bill"],
            ["name" => "delete bill"],
            ["name" => "view bill"],

            ["name" => "manage diary"],

            ["name" => "manage timesheet"],
            ["name" => "create timesheet"],
            ["name" => "edit timesheet"],
            ["name" => "delete timesheet"],
            ["name" => "view timesheet"],

            ["name" => "manage expense"],
            ["name" => "create expense"],
            ["name" => "edit expense"],
            ["name" => "delete expense"],
            ["name" => "view expense"],

            ["name" => "manage feereceived"],
            ["name" => "create feereceived"],
            ["name" => "edit feereceived"],
            ["name" => "delete feereceived"],
            ["name" => "view feereceived"],

            ["name" => "view calendar"],

            ["name" => "manage appointment"],
            ["name" => "create appointment"],
            ["name" => "edit appointment"],
            ["name" => "delete appointment"],

        ];

        $employeeRole->givePermissionTo($employeePermissions);
    }
}
