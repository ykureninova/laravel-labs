<?php

use App\Models\Project;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('project.{id}', function ($user, $id) {
    $project = Project::find($id);

    if (! $project) {
        return false;
    }

    return $project->owner_id === $user->id
        || $project->users()->where('user_id', $user->id)->exists();
});
