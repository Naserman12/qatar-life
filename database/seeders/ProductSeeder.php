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
    Product::create(['name' => 'قطرة حياة','pack' =>48,'size' =>'330', 'price' => 20]);
    Product::create(['name' => 'ندى','pack' =>40,'size' =>'550','price' => 15]);
    Product::create(['name' => 'حا','pack' =>20,'size' =>'240', 'price' => 8]);
}


}
