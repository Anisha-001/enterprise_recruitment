<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Headquarters', 'city' => 'New York', 'state' => 'New York', 'country' => 'United States', 'country_code' => 'US', 'type' => 'headquarters'],
            ['name' => 'West Coast Office', 'city' => 'San Francisco', 'state' => 'California', 'country' => 'United States', 'country_code' => 'US', 'type' => 'branch'],
            ['name' => 'London Office', 'city' => 'London', 'state' => 'England', 'country' => 'United Kingdom', 'country_code' => 'GB', 'type' => 'branch'],
            ['name' => 'Berlin Office', 'city' => 'Berlin', 'state' => 'Berlin', 'country' => 'Germany', 'country_code' => 'DE', 'type' => 'branch'],
            ['name' => 'Toronto Office', 'city' => 'Toronto', 'state' => 'Ontario', 'country' => 'Canada', 'country_code' => 'CA', 'type' => 'branch'],
            ['name' => 'Remote', 'city' => 'Remote', 'state' => 'N/A', 'country' => 'Global', 'country_code' => 'WW', 'type' => 'remote'],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(
                ['name' => $loc['name'], 'city' => $loc['city']],
                array_merge($loc, ['is_active' => true])
            );
        }
    }
}
