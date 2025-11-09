<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function indexByTask($id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeProjectAccess($task->project);
        return $task->comments;
    }

    public function storeInTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeProjectAccess($task->project);

        $data = $request->validate(['body' => 'required|string']);
        $comment = Comment::create([
            'task_id' => $task->id,
            'author_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return response()->json($comment, 201);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->author_id !== auth()->id()) {
            abort(403, 'Only author can delete comment');
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted']);
    }

    private function authorizeProjectAccess($project)
    {
        if (!auth()->user()->projects->contains($project->id) && $project->owner_id !== auth()->id()) {
            abort(403, 'Access denied');
        }
    }
}
