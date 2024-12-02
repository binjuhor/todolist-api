<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'sanctum');
});


it('returns task list', function () {
    Task::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',  // Corrected from 'tile'
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
});

it('creates a new task successfully', function () {
    $taskData = [
        'title' => 'New Task',
        'description' => 'Task description',
        'completed' => 0,
    ];

    $response = $this->postJson('/api/tasks', $taskData);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Task Created Sucessfully',
            'data' => [
                'title' => 'New Task',
                'description' => 'Task description',
                'completed' => 0,
            ],
        ]);

    $this->assertDatabaseHas('tasks', $taskData);
});

it('returns validation error for invalid data', function () {
    $taskData = [
        'title' => 'No',
        'description' => '',
        'completed' => 2,
    ];

    $response = $this->postJson('/api/tasks', $taskData);

    $response->assertStatus(500)
        ->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'title' => ['The title field must be at least 3 characters.'],
                'description' => ['The description field is required.'],
                'completed' => ['The selected completed is invalid.'],
            ],
        ]);
});

it('handles exception during task creation', function () {
    $taskData = [
        'title' => 'New Task',
        'description' => 'Task description',
        'completed' => 0,
    ];

    Validator::shouldReceive('make')->andThrow(new \Exception('Database error'));

    $response = $this->postJson('/api/tasks', $taskData);

    $response->assertStatus(500)
        ->assertJson([
            'message' => 'Database error',
        ]);
});
