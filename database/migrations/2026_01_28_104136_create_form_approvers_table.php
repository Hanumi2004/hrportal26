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
        Schema::create('form_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();  // link to master form
            $table->string('employee_id');      // employee requesting
            $table->string('approver_id');      // who approves
            $table->unsignedTinyInteger('level');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();
            $table->foreign('approver_id')->references('employee_id')->on('employees')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_approvers');
    }
};
