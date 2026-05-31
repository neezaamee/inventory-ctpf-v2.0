<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all permissions
        $permissions = [
            'manage-users',
            'manage-staff',
            'configure-inventory',
            'create-grn',
            'approve-grn',
            'create-issuance',
            'approve-issuance',
            'process-return',
            'manage-suppliers',
            'view-reports',
            'view-audit-logs',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 2. Define roles and map permissions
        $rolePermissions = [
            'Super Admin' => $permissions, // All permissions
            
            'Admin' => [
                'manage-staff',
                'configure-inventory',
                'approve-grn',
                'approve-issuance',
                'manage-suppliers',
                'view-reports',
                'view-audit-logs',
            ],
            
            'Store Incharge' => [
                'manage-staff',
                'create-grn',
                'approve-grn',
                'create-issuance',
                'approve-issuance',
                'process-return',
                'view-reports',
            ],
            
            'Store Clerk' => [
                'create-grn',
                'create-issuance',
                'process-return',
                'view-reports',
            ],
            
            'Accountant' => [
                'manage-suppliers',
                'view-reports',
            ],
            
            'Officer/Staff' => [
                'view-reports', // Limited self view handled in policy/UI
            ],
            
            'Viewer' => [
                'view-reports',
                'view-audit-logs',
            ],
        ];

        // Create roles and assign permissions
        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($perms);
        }

        // 3. Create default administrative accounts for testing
        $users = [
            [
                'name' => 'CPO Traffic Police Faisalabad',
                'email' => 'cpo@ctpf.gov.pk',
                'role' => 'Super Admin',
            ],
            [
                'name' => 'DSP HQ Faisalabad',
                'email' => 'dsp.hq@ctpf.gov.pk',
                'role' => 'Admin',
            ],
            [
                'name' => 'Store Incharge Sub-Inspector',
                'email' => 'incharge@ctpf.gov.pk',
                'role' => 'Store Incharge',
            ],
            [
                'name' => 'Store Clerk Head Constable',
                'email' => 'clerk@ctpf.gov.pk',
                'role' => 'Store Clerk',
            ],
            [
                'name' => 'Accountant Officer',
                'email' => 'accountant@ctpf.gov.pk',
                'role' => 'Accountant',
            ],
            [
                'name' => 'Internal Auditor Viewer',
                'email' => 'auditor@ctpf.gov.pk',
                'role' => 'Viewer',
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt('password123'), // Secure default dev password
            ]);
            $user->assignRole($userData['role']);
        }
    }
}
