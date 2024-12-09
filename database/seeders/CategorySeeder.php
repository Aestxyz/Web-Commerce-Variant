<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'T-Shirt Casual',],
            ['name' => 'T-Shirt Premium',],
            ['name' => 'Jersey Baseball',],
            ['name' => 'Casual Wear',],
            ['name' => 'Sneakers'],
            ['name' => 'Socks'],
            ['name' => 'Sandals'],
            ['name' => 'Jerseys'],
            ['name' => 'Sweater & Hoodie'], // Tambahan kategori baru
        ];

        foreach ($categories as $category) {
            Category::insert($category);
        }
    }
}
