<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\Project;
use App\Models\Report;
use Illuminate\Support\Carbon;

Artisan::command('app:generate-report {--file : Also store JSON file in storage/app/reports}', function () {

    $periodEnd = Carbon::now()->startOfDay();
    $periodStart = (clone $periodEnd)->subDays(7);

    $projects = Project::with('tasks')->get();

    $reportData = [
        'period_start' => $periodStart->toDateString(),
        'period_end' => $periodEnd->toDateString(),
        'generated_at' => now()->toDateTimeString(),
        'projects' => [],
    ];

    foreach ($projects as $project) {
        $statusCounts = [
            'todo' => 0,
            'in_progress' => 0,
            'done' => 0,
            'expired' => 0,
        ];

        foreach ($project->tasks as $task) {
            $status = $task->status;

            if (!array_key_exists($status, $statusCounts)) {
                if ($status === 'open') {
                    $status = 'todo';
                } else {
                    continue;
                }
            }

            $statusCounts[$status]++;
        }

        $reportData['projects'][] = [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'tasks_by_status' => $statusCounts,
        ];
    }

    $filePath = null;

    if ($this->option('file')) {
        $directory = storage_path('app/reports');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = 'report_' . now()->format('Ymd_His') . '.json';
        $fullPath = $directory . '/' . $filename;

        File::put($fullPath, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $filePath = 'reports/' . $filename;
    }

    Report::create([
        'period_start' => $periodStart,
        'period_end' => $periodEnd,
        'payload' => $reportData,
        'path' => $filePath,
    ]);

    $this->info('Report generated successfully.');
})->purpose('Generate tasks-by-status report');
