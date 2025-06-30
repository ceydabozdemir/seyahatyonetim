<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the travel dashboard page renders successfully.
     */
    public function test_dashboard_page_renders_correctly(): void
    {
        $user = User::factory()->create(); // Test için bir kullanıcı oluştur

        $response = $this->actingAs($user)->get(route('dashboard')); // Dashboard sayfasına giriş yaparak erişim sağla

        $response->assertStatus(200); // Sayfa düzgün yüklendi mi kontrol et
    }
}

