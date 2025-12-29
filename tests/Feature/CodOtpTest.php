<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class CodOtpTest extends TestCase
{
    public function test_customer_can_generate_and_delivery_can_verify_cod_otp(): void
    {
        /** @var \App\Models\User $customer */
        $customer = User::factory()->create();
        $this->actingAs($customer, 'web');

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'payment_method' => 'COD',
            'status' => 'processing',
            'email' => $customer->email,
        ]);

        // Generate COD OTP
        $response = $this->post('/orders/' . $order->id . '/cod/generate-otp');
        $response->assertRedirect();

        // Confirm OTP exists
        $key = 'otp:cod_verification:' . hash('sha256', 'order-' . $order->id);
        $this->assertTrue(Cache::has($key));
        $code = Cache::get($key)['code'] ?? null;
        $this->assertNotNull($code);

        // Delivery verifies
        $response = $this->post('/cod/verify', [
            'order_id' => $order->id,
            'otp' => $code,
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
