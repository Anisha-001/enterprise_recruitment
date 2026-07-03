<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            // Engineering
            ['name' => 'Junior Software Engineer', 'code' => 'JSE', 'department' => 'Engineering', 'level' => 'entry'],
            ['name' => 'Software Engineer', 'code' => 'SE', 'department' => 'Engineering', 'level' => 'mid'],
            ['name' => 'Senior Software Engineer', 'code' => 'SSE', 'department' => 'Engineering', 'level' => 'senior'],
            ['name' => 'Lead Software Engineer', 'code' => 'LSE', 'department' => 'Engineering', 'level' => 'lead'],
            ['name' => 'Engineering Manager', 'code' => 'EM', 'department' => 'Engineering', 'level' => 'manager'],
            ['name' => 'Director of Engineering', 'code' => 'DOE', 'department' => 'Engineering', 'level' => 'director'],
            ['name' => 'VP of Engineering', 'code' => 'VPE', 'department' => 'Engineering', 'level' => 'vp'],
            ['name' => 'DevOps Engineer', 'code' => 'DE', 'department' => 'Engineering', 'level' => 'mid'],
            ['name' => 'QA Engineer', 'code' => 'QAE', 'department' => 'Quality Assurance', 'level' => 'mid'],
            ['name' => 'Senior QA Engineer', 'code' => 'SQAE', 'department' => 'Quality Assurance', 'level' => 'senior'],

            // Product
            ['name' => 'Associate Product Manager', 'code' => 'APM', 'department' => 'Product', 'level' => 'entry'],
            ['name' => 'Product Manager', 'code' => 'PM', 'department' => 'Product', 'level' => 'mid'],
            ['name' => 'Senior Product Manager', 'code' => 'SPM', 'department' => 'Product', 'level' => 'senior'],
            ['name' => 'Director of Product', 'code' => 'DOP', 'department' => 'Product', 'level' => 'director'],

            // Design
            ['name' => 'UI/UX Designer', 'code' => 'UXD', 'department' => 'Design', 'level' => 'mid'],
            ['name' => 'Senior UI/UX Designer', 'code' => 'SUXD', 'department' => 'Design', 'level' => 'senior'],
            ['name' => 'Design Lead', 'code' => 'DL', 'department' => 'Design', 'level' => 'lead'],

            // Data
            ['name' => 'Data Analyst', 'code' => 'DA', 'department' => 'Data Science', 'level' => 'mid'],
            ['name' => 'Data Scientist', 'code' => 'DS', 'department' => 'Data Science', 'level' => 'senior'],
            ['name' => 'Senior Data Scientist', 'code' => 'SDS', 'department' => 'Data Science', 'level' => 'senior'],

            // Sales
            ['name' => 'Sales Development Rep', 'code' => 'SDR', 'department' => 'Sales', 'level' => 'entry'],
            ['name' => 'Account Executive', 'code' => 'AE', 'department' => 'Sales', 'level' => 'mid'],
            ['name' => 'Sales Manager', 'code' => 'SM', 'department' => 'Sales', 'level' => 'manager'],

            // Marketing
            ['name' => 'Marketing Specialist', 'code' => 'MS', 'department' => 'Marketing', 'level' => 'mid'],
            ['name' => 'Senior Marketing Manager', 'code' => 'SMM', 'department' => 'Marketing', 'level' => 'senior'],
            ['name' => 'Content Strategist', 'code' => 'CS', 'department' => 'Marketing', 'level' => 'mid'],

            // Customer Success
            ['name' => 'Customer Support Specialist', 'code' => 'CSS', 'department' => 'Customer Success', 'level' => 'entry'],
            ['name' => 'Customer Success Manager', 'code' => 'CSM', 'department' => 'Customer Success', 'level' => 'mid'],

            // HR
            ['name' => 'HR Specialist', 'code' => 'HRS', 'department' => 'Human Resources', 'level' => 'mid'],
            ['name' => 'HR Manager', 'code' => 'HRM', 'department' => 'Human Resources', 'level' => 'manager'],
            ['name' => 'Talent Acquisition Specialist', 'code' => 'TAS', 'department' => 'Human Resources', 'level' => 'mid'],

            // Finance
            ['name' => 'Financial Analyst', 'code' => 'FA', 'department' => 'Finance', 'level' => 'mid'],
            ['name' => 'Senior Financial Analyst', 'code' => 'SFA', 'department' => 'Finance', 'level' => 'senior'],

            // Operations
            ['name' => 'Operations Manager', 'code' => 'OM', 'department' => 'Operations', 'level' => 'manager'],
            ['name' => 'Business Analyst', 'code' => 'BA', 'department' => 'Operations', 'level' => 'mid'],
        ];

        foreach ($designations as $desig) {
            $department = Department::where('name', $desig['department'])->first();
            Designation::firstOrCreate(
                ['code' => $desig['code']],
                [
                    'name' => $desig['name'],
                    'slug' => Str::slug($desig['name']),
                    'department_id' => $department?->id,
                    'level' => $desig['level'],
                    'is_active' => true,
                ]
            );
        }
    }
}
