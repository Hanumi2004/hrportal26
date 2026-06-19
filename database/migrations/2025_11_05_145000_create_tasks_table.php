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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();   // add foreign key column
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('task_name');
            $table->text('task_desc')->nullable();
            $table->enum('task_status', ['to-do', 'in-progress', 'in-review','to-review', 'completed'])->default('to-do');
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
