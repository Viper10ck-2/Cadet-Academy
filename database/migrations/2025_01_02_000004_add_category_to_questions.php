<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Make exam_id nullable so questions can exist independently (Bank Soal)
            $table->foreignId('exam_id')->nullable()->change();
            // Add category for TIU, TWK, TKP, TBI classification
            $table->enum('category', ['TIU', 'TWK', 'TKP', 'TBI'])->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('exam_id')->nullable(false)->change();
            $table->dropColumn('category');
        });
    }
};
