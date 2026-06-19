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
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();

            $table->string('employee_id')->nullable();

            // before event 
            $table->enum('response_status', ['pending', 'confirmed', 'declined'])->default('pending');
            $table->text('decline_reason')->nullable();
            $table->timestamp('responded_at')->nullable();

            // after event
            $table->enum('attendance_status', ['attended', 'absent', 'excused'])->nullable();

            $table->text('notes')->nullable();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['event_id', 'department_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendees');
    }
};
