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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');  // add foreign key column
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->decimal('time_in_lat', 10, 7)->nullable();
            $table->decimal('time_in_lng', 10, 7)->nullable();
            $table->string('location_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('time_out_lat', 10, 7)->nullable();
            $table->decimal('time_out_lng', 10, 7)->nullable();
            $table->string('location_out')->nullable();
            $table->string('status_time_in')->nullable();  // on-time, late
            $table->string('status_time_out')->nullable(); // early, normal
            $table->string('late_reason')->nullable();
            $table->string('early_leave_reason')->nullable();
            $table->time('time_slip_start')->nullable();
            $table->time('time_slip_end')->nullable();
            $table->string('time_slip_reason')->nullable();
            $table->enum('time_slip_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
