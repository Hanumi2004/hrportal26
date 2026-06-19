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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');  // add foreign key column
            $table->foreignId('leave_entitlement_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('leave_length', ['full_day', 'AM', 'PM']);
            $table->text('leave_reason')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days');
            $table->string('attachment')->nullable();

            $table->string('approved_by')->nullable();  // add foreign key column
            $table->unsignedTinyInteger('approval_level')->default(0);
            $table->timestamp('approved_at')->nullable();

            $table->enum('leave_status', ['pending', 'approved', 'rejected'])->default('pending');
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
        Schema::dropIfExists('leaves');
    }
};
