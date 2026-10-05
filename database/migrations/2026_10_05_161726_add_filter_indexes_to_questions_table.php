<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->index('subject_id');
            $table->index('difficulty_level');
            $table->index('grading_rule');
        });

        DB::statement(
            "CREATE INDEX questions_search_idx ON questions
            USING GIN (to_tsvector('indonesian', question_text))"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS questions_search_idx');

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['subject_id']);
            $table->dropIndex(['difficulty_level']);
            $table->dropIndex(['grading_rule']);
        });
    }
};
