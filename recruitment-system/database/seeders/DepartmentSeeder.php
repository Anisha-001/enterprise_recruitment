<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Engineering', 'code' => 'ENG', 'description' => 'Software development and technical operations'],
            ['name' => 'Product', 'code' => 'PROD', 'description' => 'Product management and strategy'],
            ['name' => 'Design', 'code' => 'DES', 'description' => 'UI/UX design and creative services'],
            ['name' => 'Marketing', 'code' => 'MKT', 'description' => 'Brand, growth, and digital marketing'],
            ['name' => 'Sales', 'code' => 'SAL', 'description' => 'Business development and sales operations'],
            ['name' => 'Customer Success', 'code' => 'CS', 'description' => 'Customer support and success management'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'People operations and talent management'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Financial planning and accounting'],
            ['name' => 'Operations', 'code' => 'OPS', 'description' => 'Business operations and logistics'],
            ['name' => 'Legal', 'code' => 'LEG', 'description' => 'Legal and compliance'],
            ['name' => 'Data Science', 'code' => 'DS', 'description' => 'Data analytics and machine learning'],
            ['name' => 'Quality Assurance', 'code' => 'QA', 'description' => 'Quality assurance and testing'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                [
                    'name' => $dept['name'],
                    'slug' => Str::slug($dept['name']),
                    'description' => $dept['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
