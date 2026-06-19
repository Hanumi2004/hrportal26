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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('form_type');
            $table->string('employee_id');
            $table->text('form_description')->nullable();

            $table->string('approved_by')->nullable();
            $table->unsignedTinyInteger('approval_level')->default(0);
            $table->timestamp('approval_at')->nullable();

            $table->enum('form_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reject_reason')->nullable();
            
            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();  // when the parent record is deleted, the child is deleted
            $table->foreign('approved_by')->references('employee_id')->on('employees')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
