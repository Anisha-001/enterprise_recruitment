<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // Technical Skills
            ['name' => 'PHP', 'category' => 'technical'],
            ['name' => 'Laravel', 'category' => 'framework'],
            ['name' => 'Python', 'category' => 'technical'],
            ['name' => 'JavaScript', 'category' => 'technical'],
            ['name' => 'TypeScript', 'category' => 'technical'],
            ['name' => 'React', 'category' => 'framework'],
            ['name' => 'Vue.js', 'category' => 'framework'],
            ['name' => 'Node.js', 'category' => 'technical'],
            ['name' => 'Docker', 'category' => 'devops'],
            ['name' => 'Kubernetes', 'category' => 'devops'],
            ['name' => 'AWS', 'category' => 'cloud'],
            ['name' => 'Azure', 'category' => 'cloud'],
            ['name' => 'GCP', 'category' => 'cloud'],
            ['name' => 'MySQL', 'category' => 'database'],
            ['name' => 'PostgreSQL', 'category' => 'database'],
            ['name' => 'MongoDB', 'category' => 'database'],
            ['name' => 'Redis', 'category' => 'database'],
            ['name' => 'Git', 'category' => 'tool'],
            ['name' => 'CI/CD', 'category' => 'devops'],
            ['name' => 'Terraform', 'category' => 'devops'],
            ['name' => 'Machine Learning', 'category' => 'technical'],
            ['name' => 'Data Analysis', 'category' => 'technical'],
            ['name' => 'REST API', 'category' => 'technical'],
            ['name' => 'GraphQL', 'category' => 'technical'],

            // Soft Skills
            ['name' => 'Communication', 'category' => 'soft'],
            ['name' => 'Leadership', 'category' => 'soft'],
            ['name' => 'Problem Solving', 'category' => 'soft'],
            ['name' => 'Teamwork', 'category' => 'soft'],
            ['name' => 'Time Management', 'category' => 'soft'],
            ['name' => 'Critical Thinking', 'category' => 'soft'],
            ['name' => 'Adaptability', 'category' => 'soft'],
            ['name' => 'Creativity', 'category' => 'soft'],
            ['name' => 'Emotional Intelligence', 'category' => 'soft'],

            // Languages
            ['name' => 'English', 'category' => 'language'],
            ['name' => 'Spanish', 'category' => 'language'],
            ['name' => 'French', 'category' => 'language'],
            ['name' => 'German', 'category' => 'language'],
            ['name' => 'Mandarin', 'category' => 'language'],
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(
                ['name' => $skill['name'], 'category' => $skill['category']],
                ['slug' => Str::slug($skill['name']), 'is_active' => true]
            );
        }
    }
}
