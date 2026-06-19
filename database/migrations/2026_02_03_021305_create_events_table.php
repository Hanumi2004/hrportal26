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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('event_name');
            $table->text('description');
            $table->date('event_date');
            $table->time('event_time');
            $table->string('event_location');
            $table->foreignId('event_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image')->nullable();
            $table->enum('event_status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->string('tags')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
