<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a new user successfully', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
            'token',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
});

it('returns validation error for invalid data', function () {
    $invalidData = [
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'pass',
        'password_confirmation' => 'different',
    ];

    $response = $this->postJson('/api/register', $invalidData);

    $response->assertStatus(500)
        ->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'name' => ['The name field is required.'],
                'email' => ['The email field must be a valid email address.'],
                'password' => ['The password field confirmation does not match.'],
            ],
        ]);
});
