<?php

namespace Tests\Feature;

use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        SmtpSetting::create([
            'mailer' => 'log',
            'host' => '127.0.0.1',
            'port' => 2525,
            'from_address' => 'support@fixsmith.com',
            'from_name' => 'Fixsmith Repair Studio',
        ]);

        User::create([
            'name' => 'Maya Chen',
            'email' => 'maya.chen@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
