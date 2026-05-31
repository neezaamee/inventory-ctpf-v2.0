<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Staff;

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

        $staffProfiles = [
            'Super Admin' => [
                'first_name' => 'CPO Traffic',
                'last_name' => 'Police',
                'belt_no' => '1001',
                'cnic' => '33100-0000001-1',
                'rank' => 'Chief Traffic Officer',
                'gender' => 'male',
                'phone_no' => '0300-0000001',
                'current_posting' => 'CTPF HQ, Faisalabad',
                'status' => 'active',
            ],
            'Admin' => [
                'first_name' => 'DSP HQ',
                'last_name' => 'Faisalabad',
                'belt_no' => '1002',
                'cnic' => '33100-0000002-2',
                'rank' => 'DSP',
                'gender' => 'male',
                'phone_no' => '0300-0000002',
                'current_posting' => 'CTPF HQ, Faisalabad',
                'status' => 'active',
            ],
            'Store Incharge' => [
                'first_name' => 'Store',
                'last_name' => 'Incharge',
                'belt_no' => '1003',
                'cnic' => '33100-0000003-3',
                'rank' => 'Sub-Inspector',
                'gender' => 'male',
                'phone_no' => '0300-0000003',
                'current_posting' => 'Wardi Store, Faisalabad',
                'status' => 'active',
            ],
            'Store Clerk' => [
                'first_name' => 'Store',
                'last_name' => 'Clerk',
                'belt_no' => '1004',
                'cnic' => '33100-0000004-4',
                'rank' => 'Head Constable',
                'gender' => 'male',
                'phone_no' => '0300-0000004',
                'current_posting' => 'Wardi Store, Faisalabad',
                'status' => 'active',
            ],
            'Accountant' => [
                'first_name' => 'Accountant',
                'last_name' => 'Officer',
                'belt_no' => '1005',
                'cnic' => '33100-0000005-5',
                'rank' => 'Accountant',
                'gender' => 'male',
                'phone_no' => '0300-0000005',
                'current_posting' => 'Accounts Branch, Faisalabad',
                'status' => 'active',
            ],
            'Viewer' => [
                'first_name' => 'Internal',
                'last_name' => 'Auditor',
                'belt_no' => '1006',
                'cnic' => '33100-0000006-6',
                'rank' => 'Inspector',
                'gender' => 'male',
                'phone_no' => '0300-0000006',
                'current_posting' => 'Audit Branch, Faisalabad',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            $staff = Staff::create($staffProfiles[$userData['role']]);
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt('password123'), // Secure default dev password
                'staff_id' => $staff->id,
            ]);
            $user->assignRole($userData['role']);
        }
    }
}
