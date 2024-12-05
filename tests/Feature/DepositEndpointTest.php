<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DepositEndpointTest extends TestCase
{
    public function test_deposit_endpoint()
    {
        $response = $this->postJson('/api/deposit', [
            'order_id' => 'ORDER-12345',
            'amount' => 100.00,
            'timestamp' => now()->toIso8601String(),
        ], [
            'Authorization' => 'Bearer ' . base64_encode('Lukman Hakim'),
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'order_id',
                'amount',
                'timestamp',
            ]);
    }
}
