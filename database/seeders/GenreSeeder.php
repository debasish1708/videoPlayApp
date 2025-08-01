<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            ['name' => 'Entertainment', 'slug' => 'entertainment'],
            ['name' => 'Fantasy', 'slug' => 'fantasy'],
            ['name' => 'Comedy', 'slug' => 'comedy'],
            ['name' => 'Slasher', 'slug' => 'slasher'],
            ['name' => 'Most Popular', 'slug' => 'most-popular']
        ];
        foreach ($genres as $genre) {
            \App\Models\Genre::create($genre);
        }
    }
}
