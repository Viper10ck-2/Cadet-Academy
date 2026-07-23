<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // JSON array of user IDs allowed to participate
            $table->json('participant_ids')->nullable()->after('type');
            // JSON question composition: {"TWK":30, "TIU":35, "TKP":45}
            $table->json('question_composition')->nullable()->after('participant_ids');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['participant_ids', 'question_composition']);
        });
    }
};
