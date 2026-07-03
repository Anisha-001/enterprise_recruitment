<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Application permissions
            'applications.view',
            'applications.create',
            'applications.edit',
            'applications.delete',
            'applications.export',
            'applications.manage_interviews',
            'applications.create_offers',
            'applications.add_notes',

            // Job permissions
            'jobs.view',
            'jobs.create',
            'jobs.edit',
            'jobs.delete',
            'jobs.publish',
            'jobs.clone',
            'jobs.close',
            'jobs.reopen',

            // Candidate permissions
            'candidates.view',
            'candidates.create',
            'candidates.edit',
            'candidates.blacklist',

            // Interview permissions
            'interviews.view',
            'interviews.create',
            'interviews.edit',
            'interviews.cancel',
            'interviews.provide_feedback',

            // Offer permissions
            'offers.view',
            'offers.create',
            'offers.send',
            'offers.accept',
            'offers.reject',

            // Settings permissions
            'settings.view',
            'settings.edit',

            // Reports permissions
            'reports.view',
            'reports.export',

            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $hrAdmin = Role::firstOrCreate(['name' => 'hr_admin', 'guard_name' => 'web']);
        $recruiter = Role::firstOrCreate(['name' => 'recruiter', 'guard_name' => 'web']);
        $hiringManager = Role::firstOrCreate(['name' => 'hiring_manager', 'guard_name' => 'web']);
        $interviewer = Role::firstOrCreate(['name' => 'interviewer', 'guard_name' => 'web']);

        // Assign permissions to roles
        $superAdmin->syncPermissions(Permission::all());

        $hrAdmin->syncPermissions([
            'applications.view', 'applications.create', 'applications.edit', 'applications.delete', 'applications.export',
            'applications.manage_interviews', 'applications.create_offers', 'applications.add_notes',
            'jobs.view', 'jobs.create', 'jobs.edit', 'jobs.delete', 'jobs.publish', 'jobs.clone', 'jobs.close', 'jobs.reopen',
            'candidates.view', 'candidates.create', 'candidates.edit', 'candidates.blacklist',
            'interviews.view', 'interviews.create', 'interviews.edit', 'interviews.cancel', 'interviews.provide_feedback',
            'offers.view', 'offers.create', 'offers.send', 'offers.accept', 'offers.reject',
            'settings.view', 'settings.edit',
            'reports.view', 'reports.export',
            'users.view', 'users.create', 'users.edit',
        ]);

        $recruiter->syncPermissions([
            'applications.view', 'applications.create', 'applications.edit', 'applications.add_notes',
            'applications.manage_interviews', 'applications.create_offers',
            'jobs.view', 'jobs.create', 'jobs.edit', 'jobs.publish', 'jobs.clone', 'jobs.close', 'jobs.reopen',
            'candidates.view', 'candidates.create', 'candidates.edit',
            'interviews.view', 'interviews.create', 'interviews.edit', 'interviews.cancel',
            'offers.view', 'offers.create', 'offers.send',
            'reports.view',
        ]);

        $hiringManager->syncPermissions([
            'applications.view', 'applications.edit', 'applications.add_notes',
            'applications.manage_interviews', 'applications.create_offers',
            'jobs.view', 'jobs.edit',
            'candidates.view',
            'interviews.view', 'interviews.create', 'interviews.provide_feedback',
            'offers.view', 'offers.create',
            'reports.view',
        ]);

        $interviewer->syncPermissions([
            'applications.view',
            'interviews.view', 'interviews.provide_feedback',
        ]);

        $this->command->info('Roles and permissions created successfully.');
    }
}
