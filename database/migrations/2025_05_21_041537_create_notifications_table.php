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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type'); // 'match_reminder', 'payment_confirmation', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // Related game_id, order_id, etc.
            $table->string('reference_type')->nullable(); // Polymorphic: 'App\Models\Game', 'App\Models\Order', etc.
            $table->boolean('is_read')->default(false);
            $table->json('data')->nullable(); // Additional data in JSON format
            $table->timestamp('scheduled_at')->nullable(); // For scheduled notifications
            $table->timestamp('sent_at')->nullable(); // When the notification was sent
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('user_id');
            $table->index('is_read');
            $table->index(['reference_id', 'reference_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
