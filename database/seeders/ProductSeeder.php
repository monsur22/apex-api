<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Create a few products
        Product::create([
            'name' => 'Product 1',
            'price' => 29.99,
            'stock' => 100,
        ]);

        Product::create([
            'name' => 'Product 2',
            'price' => 49.99,
            'stock' => 50,
        ]);

        Product::create([
            'name' => 'Product 3',
            'price' => 19.99,
            'stock' => 200,
        ]);
    }
}
