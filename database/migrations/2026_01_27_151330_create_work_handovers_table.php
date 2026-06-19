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
        Schema::create('work_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->date('last_working_day');
            $table->string('handover_to')->constrained('users')->cascadeOnDelete()->nullable();
            $table->string('handover_reason');
            $table->text('handover_notes')->nullable();
            $table->json('tasks')->nullable();
            $table->json('documents')->nullable();
            $table->json('electronic_files')->nullable();
            $table->json('passwords')->nullable();
            $table->json('financial_commitments')->nullable();
            $table->json('inventory')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_handovers');
    }
};
