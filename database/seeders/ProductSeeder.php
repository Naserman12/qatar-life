<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run(): void
{
    Product::create(['name' => 'Product 1','pack' =>48,'size' =>'330', 'price' => 20]);
    Product::create(['name' => 'Product 2','pack' =>40,'size' =>'550','price' => 15]);
    Product::create(['name' => 'Product 3','pack' =>20,'size' =>'240', 'price' => 8]);
}


}
