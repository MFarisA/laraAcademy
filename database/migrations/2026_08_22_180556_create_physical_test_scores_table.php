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
        Schema::create('physical_test_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('physical_assessment_id')->constrained('physical_assessments')->cascadeOnDelete();
            $table->string('metric_name', 100);
            $table->string('raw_value', 50);
            $table->decimal('calculated_score', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_test_scores');
    }
};
