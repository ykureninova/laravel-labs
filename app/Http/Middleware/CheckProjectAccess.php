<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Project;

class CheckProjectAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $projectId = $request->route('id');
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $user = $request->user();

        $hasAccess = $project->owner_id === $user->id ||
            $project->users()->where('user_id', $user->id)->exists();

        if (!$hasAccess) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
