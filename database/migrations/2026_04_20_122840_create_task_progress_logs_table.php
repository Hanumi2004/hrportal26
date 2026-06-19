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
         Schema::create('task_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->string('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();
            $table->string('employee_status')->default('pending');
            $table->text('employee_remarks')->nullable();
            $table->timestamp('progress_updated_at')->nullable();
            $table->timestamps();
    	});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_progress_logs');
    }
};
