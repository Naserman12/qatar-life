<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
     use RefreshDatabase; 
    /**
     * A basic feature test example.
     */
     #[\PHPUnit\Framework\Attributes\Test]
   public function test_user_can_add_to_cart()
{
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $cartItem = Cart::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->assertDatabaseHas('carts', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
}

}
