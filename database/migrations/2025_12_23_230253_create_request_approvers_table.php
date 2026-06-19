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
        Schema::create('request_approvers', function (Blueprint $table) {
            $table->id();
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
        Schema::dropIfExists('request_approvers');
    }
};
