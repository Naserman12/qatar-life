<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

     #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_confirm_order_and_delete_from_cart()
    {
        $admin = User::factory()->create();
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson("/api/admin/orders/{$order->id}/confirm");

        $response->assertStatus(200)
                 ->assertJson(['message' => '✅ تم تأكيد الطلب وحذفه من السلة']);

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id
        ]);
    }
}
