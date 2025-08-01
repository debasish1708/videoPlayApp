<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $access_types = [
            ['name' => 'Free', 'slug' => 'free'],
            ['name' => 'Paid', 'slug' => 'paid'],
            ['name' => 'Ad Supported', 'slug' => 'ad-supported']
        ];
        foreach ($access_types as $access_type) {
            \App\Models\AccessType::create($access_type);
        }
    }
}
