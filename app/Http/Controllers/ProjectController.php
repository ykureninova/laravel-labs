<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->projects()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create([
            'owner_id' => $request->user()->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $project->users()->attach($request->user()->id, ['role' => 'owner']);

        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        $this->authorizeProjectAccess($project);
        return $project->load('tasks');
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeOwner($project);
        $project->update($request->only('name', 'description'));
        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $this->authorizeOwner($project);
        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }

    private function authorizeProjectAccess(Project $project)
    {
        if (!auth()->user()->projects->contains($project->id) && $project->owner_id !== auth()->id()) {
            abort(403, 'Access denied');
        }
    }

    private function authorizeOwner(Project $project)
    {
        if ($project->owner_id !== auth()->id()) {
            abort(403, 'Only owner can modify project');
        }
    }
}
