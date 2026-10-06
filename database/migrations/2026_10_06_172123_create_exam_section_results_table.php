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
        Schema::create('exam_section_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('exam_section_id')->constrained('exam_sections')->cascadeOnDelete();
            $table->decimal('score', 8, 2)->default(0.00);
            $table->boolean('is_passed')->default(false);
            $table->timestamps();

            $table->unique(['exam_attempt_id', 'exam_section_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_section_results');
    }
};
