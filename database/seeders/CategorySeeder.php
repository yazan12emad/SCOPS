<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Streaming', 'description' => 'Video streaming service'],
            ['name' => 'Music', 'description' => 'Music streaming service'],
            ['name' => 'Productivity', 'description' => 'Productivity tools'],
            ['name' => 'Gaming', 'description' => 'Gaming services'],
            ['name' => 'Cloud Storage', 'description' => 'Cloud storage service'],
            ['name' => 'Educational', 'description' => 'Educational platforms']
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
