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

it('retrieves a task successfully', function () {
    $task = Task::factory()->create();

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Task Retrieved Successfully',
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'created_at' => $task->created_at->toJSON(),
                'updated_at' => $task->updated_at->toJSON(),
            ],
        ]);
});

it('returns 404 if task not found', function () {
    $response = $this->getJson('/api/tasks/999');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Task Not Found',
        ]);
});

it('updates a task successfully', function () {
    $task = Task::factory()->create();

    $updatedData = [
        'title' => 'Updated Task',
        'description' => 'Updated description',
        'completed' => 1,
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Task Updated Sucessfully',
            'data' => [
                'id' => $task->id,
                'title' => 'Updated Task',
                'description' => 'Updated description',
                'completed' => 1,
            ],
        ]);

    $this->assertDatabaseHas('tasks', $updatedData);
});

it('returns validation error for update invalid data', function () {
    $task = Task::factory()->create();

    $invalidData = [
        'title' => 'No',
        'description' => '',
        'completed' => 2,
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $invalidData);

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

it('returns 404 if updated task not found', function () {
    $updatedData = [
        'title' => 'Updated Task',
        'description' => 'Updated description',
        'completed' => 1,
    ];

    $response = $this->putJson('/api/tasks/999', $updatedData);

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Task Not Found',
        ]);
});

it('handles exception during task update', function () {
    $task = Task::factory()->create();

    $updatedData = [
        'title' => 'Updated Task',
        'description' => 'Updated description',
        'completed' => 1,
    ];

    Validator::shouldReceive('make')->andThrow(new \Exception('Database error'));

    $response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

    $response->assertStatus(500)
        ->assertJson([
            'message' => 'Database error',
        ]);
});

it('returns 404 if task delete not found', function () {
    $response = $this->deleteJson('/api/tasks/999');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Task Not Found',
        ]);
});

it('deletes a task successfully', function () {
    $task = Task::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Task Deleted Sucessfully',
        ]);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
