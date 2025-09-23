<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;

     #[\PHPUnit\Framework\Attributes\Test]
    public function product_can_be_created()
    {
        $data = [
            'name' => 'مياه غارية',
            'pack' => 24,
            'size' => '330ml',
            'price' => 12.50,
            'available' => true,
            'description' => 'وصف المنتج',
            'image' => null,
            'category' => 'مشروبات',
            'tax_included' => true,
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => '✅ تم إضافة المنتج بنجاح',
                     'product' => [
                         'name' => 'مياه غارية'
                     ]
                 ]);

        $this->assertDatabaseHas('products', ['name' => 'مياه غارية']);
    }

    /** @test */
    public function product_can_be_updated()
    {
        $product = Product::create([
            'name' => 'مياه غارية',
            'pack' => 24,
            'size' => '330ml',
            'price' => 12.50,
            'available' => true,
        ]);

        $updateData = [
            'name' => 'مياه غارية - كبير',
            'pack' => 12,
            'size' => '500ml',
            'price' => 15.00,
            'available' => false,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => '✅ تم تحديث المنتج بنجاح',
                     'product' => [
                         'name' => 'مياه غارية - كبير'
                     ]
                 ]);

        $this->assertDatabaseHas('products', ['name' => 'مياه غارية - كبير', 'price' => 15.00]);
    }

    /** @test */
    public function product_can_be_deleted()
    {
        $product = Product::create([
            'name' => 'مياه غارية',
            'pack' => 24,
            'size' => '330ml',
            'price' => 12.50,
            'available' => true,
        ]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => '✅ تم حذف المنتج'
                 ]);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function can_fetch_all_products()
    {
        Product::create(['name'=>'منتج1','pack'=>12,'size'=>'330ml','price'=>10,'available'=>true]);
        Product::create(['name'=>'منتج2','pack'=>24,'size'=>'500ml','price'=>20,'available'=>true]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(2)
                 ->assertJsonFragment(['name' => 'منتج1'])
                 ->assertJsonFragment(['name' => 'منتج2']);
    }
}
