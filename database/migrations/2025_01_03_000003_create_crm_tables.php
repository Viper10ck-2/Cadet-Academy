<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Leads
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('source')->nullable(); // website, referral, social_media, event
            $table->enum('status', ['new', 'contacted', 'interested', 'registered', 'lost'])->default('new');
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();
        });

        // Campaigns / Kampanye
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('email'); // email, whatsapp, sms
            $table->text('content');
            $table->enum('status', ['draft', 'scheduled', 'sent', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('recipients_count')->default(0);
            $table->timestamps();
        });

        // Broadcasts
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('whatsapp'); // whatsapp, email, notification
            $table->json('recipient_ids')->nullable(); // specific user IDs
            $table->string('target_role')->nullable(); // all cadets, all instructors
            $table->enum('status', ['draft', 'sending', 'sent', 'failed'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Testimonials / Testimoni
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('content');
            $table->string('avatar_path')->nullable();
            $table->integer('rating')->default(5);
            $table->boolean('is_published')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('broadcasts');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('leads');
    }
};
