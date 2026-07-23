<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add type to exams (tryout_skd, mini_quiz, regular)
        Schema::table('exams', function (Blueprint $table) {
            $table->enum('type', ['tryout_skd', 'mini_quiz', 'regular'])->default('regular')->after('title');
        });

        // Add question_ids (JSON) to exam_sessions for per-cadet randomization
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->json('question_ids')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropColumn('question_ids');
        });
    }
};
