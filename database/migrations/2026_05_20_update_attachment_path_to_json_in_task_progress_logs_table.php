<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_progress_logs', function (Blueprint $table) {
            $table->text('attachment_path')->nullable()->change();
        });

        DB::table('task_progress_logs')
            ->whereNotNull('attachment_path')
            ->where('attachment_path', 'not like', '[%')
            ->get()
            ->each(function ($log) {
                DB::table('task_progress_logs')
                    ->where('id', $log->id)
                    ->update(['attachment_path' => json_encode([$log->attachment_path])]);
            });
    }

    public function down(): void
    {
        DB::table('task_progress_logs')
            ->whereNotNull('attachment_path')
            ->where('attachment_path', 'like', '[%')
            ->get()
            ->each(function ($log) {
                $paths = json_decode($log->attachment_path, true);
                $first = is_array($paths) ? ($paths[0] ?? null) : $log->attachment_path;
                DB::table('task_progress_logs')
                    ->where('id', $log->id)
                    ->update(['attachment_path' => $first]);
            });

        Schema::table('task_progress_logs', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->change();
        });
    }
};
