<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_settings_page()
    {
        $response = $this->get(route('admin.settings.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Sistem');
    }

    public function test_admin_can_update_settings()
    {
        $data = [
            'gemini_api_key' => 'new-test-key',
            'gemini_model' => 'gemini-1.5-pro',
            'default_passing_score' => 85,
        ];

        $response = $this->post(route('admin.settings.update'), $data);

        $response->assertRedirect();
        $this->assertEquals('new-test-key', Setting::get('gemini_api_key'));
        $this->assertEquals('gemini-1.5-pro', Setting::get('gemini_model'));
        $this->assertEquals(85, Setting::get('default_passing_score'));
    }
}
