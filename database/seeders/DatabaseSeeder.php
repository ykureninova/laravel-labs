<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //5 користувачів
        $users = User::factory(5)->create();

        //5 проектів і існуючих користувачів
        $projects = collect();
        for ($i = 0; $i < 5; $i++) {
            $projects->push(
                Project::factory()->create([
                    'owner_id' => $users->random()->id,
                ])
            );
        }

        //до кожного проекту учасників з таблиці project_user
        foreach ($projects as $project) {
            $members = $users->random(3);

            foreach ($members as $member) {
                $project->users()->attach($member->id, [
                    'role' => $member->id === $project->owner_id ? 'owner' : 'member',
                ]);
            }
        }

        //8 задач кожна задача посилається на існуючий проект/юзерів
        $tasks = collect();
        for ($i = 0; $i < 8; $i++) {
            $project = $projects->random();
            $author  = $users->random();
            $assignee = $users->random();

            $tasks->push(
                Task::factory()->create([
                    'project_id' => $project->id,
                    'author_id' => $author->id,
                    'assignee_id' => $assignee->id,
                ])
            );
        }

        //8 коментарів кожен коментар посилається на існуючу задачу/юзера
        for ($i = 0; $i < 8; $i++) {
            $task = $tasks->random();
            $author = $users->random();

            Comment::factory()->create([
                'task_id' => $task->id,
                'author_id' => $author->id,
            ]);
        }

        //5 reports
        for ($i = 0; $i < 5; $i++) {
            Report::create([
                'period_start' => now()->subDays(14),
                'period_end' => now(),
                'payload' => [
                    'total_tasks' => 10 + $i,
                    'done_tasks' => 5 + $i,
                    'blocked_tasks' => 1,
                ],
                'path' => 'reports/report_' . ($i + 1) . '.pdf',
            ]);
        }
    }
}
