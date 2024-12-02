<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Exception;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function index()
    {
        try {
            $tasks = Task::latest()->get();
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Failed To Retrieve Task List',
                'error' => $error->getMessage()
            ], 500);
        }

        return TaskResource::collection($tasks);
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|min:3|max:255',
                'description' => 'required|min:3|max:255',
                'completed' => 'required|in:0,1',
            ]);
        } catch (ValidationException $error) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $error->errors(),
            ], 500);
        }

        try {
            $task = Task::create($request->all());

            return response()->json([
                'message' => 'Task Created Sucessfully',
                'data' => new TaskResource($task)
            ], 201);
        } catch (Exception $error) {

            return response()->json([
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        // show detail
    }

    public function update(Request $request, string $id)
    {
        //update
    }

    public function destroy(string $id)
    {
        // destroy
    }
}
