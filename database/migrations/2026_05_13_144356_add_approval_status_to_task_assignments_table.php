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
            $table->string('approval_status')
              ->default('approved') // default approved supaya data lama tak rosak
              ->after('employee_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {
        	$table->dropColumn('approval_status');
        });
    }
					  
};
