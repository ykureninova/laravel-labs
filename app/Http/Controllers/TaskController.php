<?php

namespace App\Http\Controllers;

use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function indexByProject($id, Request $request)
    {
        $project = Project::findOrFail($id);
        $this->authorizeProjectAccess($project);

        $query = Task::where('project_id', $id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('assignee_id')) {
            $query->where('assignee_id', $request->assignee_id);
        }
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        return $query->get();
    }

    public function storeInProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorizeProjectAccess($project);

        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'assignee_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $data['project_id'] = $project->id;
        $data['author_id'] = $request->user()->id;

        $task = Task::create($data);

        TaskCreated::dispatch($task);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        $this->authorizeProjectAccess($task->project);
        return $task->load('comments');
    }

    public function update(Request $request, Task $task)
    {
        $user = $request->user();
        if ($task->author_id !== $user->id && $task->project->owner_id !== $user->id) {
            abort(403, 'Access denied');
        }

        $task->update($request->only(['title', 'description', 'status', 'priority', 'due_date']));

        TaskUpdated::dispatch($task);

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $user = auth()->user();
        if ($task->author_id !== $user->id && $task->project->owner_id !== $user->id) {
            abort(403, 'Access denied');
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    private function authorizeProjectAccess(Project $project)
    {
        if (!auth()->user()->projects->contains($project->id) && $project->owner_id !== auth()->id()) {
            abort(403, 'Access denied');
        }
    }
}
