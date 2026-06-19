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
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->string('employee_status')->nullable()->default('pending')->after('employee_id');
            $table->text('employee_remarks')->nullable()->after('employee_status');
            $table->timestamp('progress_updated_at')->nullable()->after('employee_remarks');
    	});
	}
 
	/**
 	* Reverse the migrations.
 	*/
	public function down(): void
	{
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->dropColumn(['employee_status', 'employee_remarks', 'progress_updated_at']);
    	});
	}
};
