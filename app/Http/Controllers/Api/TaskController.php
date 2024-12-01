<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Exception;

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
        // store data
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
