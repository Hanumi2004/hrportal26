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
        Schema::create('employments', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');  // add foreign key column
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employment_type_id')->nullable()->constrained()->nullOnDelete(); // full time, part time, intern, contract
            $table->foreignId('employment_status_id')->nullable()->constrained()->nullOnDelete(); // active, probation, suspended, resigned, terminated
            $table->foreignId('company_branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('report_to')->nullable();
            $table->string('position')->nullable();

            $table->date('date_of_employment')->nullable();

            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();

            $table->date('probation_start')->nullable();
            $table->date('probation_end')->nullable();

            $table->date('suspension_start')->nullable();
            $table->date('suspension_end')->nullable();

            $table->date('resignation_date')->nullable();
            $table->date('last_working_day')->nullable();

            $table->date('termination_date')->nullable();

            $table->time('work_start_time')->nullable();
            $table->time('work_end_time')->nullable();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();
            $table->foreign('report_to')->references('employee_id')->on('employees')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employments');
    }
};
