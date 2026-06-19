<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('task_assignments', 'employee_progress')) {
            Schema::table('task_assignments', function (Blueprint $table) {
                $table->dropColumn('employee_progress');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('task_assignments', 'employee_progress')) {
            Schema::table('task_assignments', function (Blueprint $table) {
                $table->unsignedInteger('employee_progress')
                      ->default(0)
                      ->after('employee_status');
            });
        }
    }
};