<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();

            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();

            $table->string('employee_id')->nullable();
            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();

            $table->timestamps();

            // Prevent duplicates
            $table->unique(['task_id', 'department_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};
