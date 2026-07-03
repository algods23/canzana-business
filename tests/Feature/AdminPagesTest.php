<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_main_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        foreach (['dashboard', 'properties.index', 'reports.index'] as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk();
        }
    }
}
