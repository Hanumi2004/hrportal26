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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->text('project_desc')->nullable();
            $table->string('created_by')->nullable(); // add foreign key column
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('project_status', ['not-started', 'in-progress', 'on-hold', 'completed'])->default('not-started');
            $table->timestamps();

            $table->foreign('created_by')->references('employee_id')->on('employees')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
