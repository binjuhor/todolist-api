<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs in a user successfully', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('password'),
    ]);

    $loginData = [
        'email' => 'john@example.com',
        'password' => 'password',
    ];

    $response = $this->postJson('/api/login', $loginData);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
            'token',
        ]);
});

it('returns validation error for invalid data', function () {
    $invalidData = [
        'email' => 'invalid-email',
        'password' => '',
    ];

    $response = $this->postJson('/api/login', $invalidData);

    $response->assertStatus(500)
        ->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => ['The email field must be a valid email address.'],
                'password' => ['The password field is required.'],
            ],
        ]);
});

it('returns error for invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('password'),
    ]);

    $invalidCredentials = [
        'email' => 'john@example.com',
        'password' => 'wrongpassword',
    ];

    $response = $this->postJson('/api/login', $invalidCredentials);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Your credentials are invalid.',
        ]);
});
