<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Storage::fake('public');
    }

    /** @test */
    public function user_can_view_settings_page()
    {
        $this->actingAs($this->user)
            ->get(route('admin.settings.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.settings.index');
    }

    /** @test */
    public function user_can_update_business_settings()
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.settings.update'), [
                'business_name' => 'Updated Business Name',
                'business_address' => '456 New Street',
                'business_city' => 'New City',
                'business_phone' => '9876543210',
                'business_email' => 'new@business.com',
            ]);

        $response->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('success');

        $this->assertEquals('Updated Business Name', Setting::get('business_name'));
        $this->assertEquals('new@business.com', Setting::get('business_email'));
    }

    /** @test */
    public function user_can_upload_site_logo()
    {
        $file = UploadedFile::fake()->image('logo.png', 200, 200);

        $response = $this->actingAs($this->user)
            ->post(route('admin.settings.update'), [
                'business_name' => 'Test Business',
                'site_logo' => $file,
            ]);

        $response->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('success');

        $logoPath = Setting::get('site_logo');
        $this->assertNotNull($logoPath);
        Storage::disk('public')->assertExists($logoPath);
    }

    /** @test */
    public function user_can_upload_favicon()
    {
        $file = UploadedFile::fake()->image('favicon.png', 32, 32);

        $response = $this->actingAs($this->user)
            ->post(route('admin.settings.update'), [
                'business_name' => 'Test Business',
                'site_favicon' => $file,
            ]);

        $response->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('success');

        $faviconPath = Setting::get('site_favicon');
        $this->assertNotNull($faviconPath);
        Storage::disk('public')->assertExists($faviconPath);
    }

    /** @test */
    public function setting_validation_rejects_invalid_logo_format()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->user)
            ->post(route('admin.settings.update'), [
                'business_name' => 'Test Business',
                'site_logo' => $file,
            ]);

        $response->assertSessionHasErrors('site_logo');
    }

    /** @test */
    public function setting_validation_rejects_oversized_logo()
    {
        // Create a 3MB file (exceeds 2MB limit)
        $file = UploadedFile::fake()->image('logo.png')->size(3000);

        $response = $this->actingAs($this->user)
            ->post(route('admin.settings.update'), [
                'business_name' => 'Test Business',
                'site_logo' => $file,
            ]);

        $response->assertSessionHasErrors('site_logo');
    }

    /** @test */
    public function settings_can_be_retrieved_with_defaults()
    {
        $value = Setting::get('business_name', 'Default Name');
        $this->assertNotNull($value);
    }

    /** @test */
    public function settings_cache_is_cleared_on_update()
    {
        Setting::set('test_key', 'old_value');
        $this->assertEquals('old_value', Setting::get('test_key'));

        Setting::set('test_key', 'new_value');
        $this->assertEquals('new_value', Setting::get('test_key'));
    }

    /** @test */
    public function guest_cannot_access_settings()
    {
        $response = $this->get(route('admin.settings.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('admin.settings.update'), [
            'business_name' => 'Test',
        ]);
        $response->assertRedirect(route('login'));
    }
}
