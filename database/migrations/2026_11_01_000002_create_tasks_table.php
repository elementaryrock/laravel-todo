<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title', 180);
            $table->text('notes')->nullable();
            $table->string('priority', 20)->default('medium'); // critical, high, medium, low
            $table->string('time_window', 20)->default('meridian'); // zenith, meridian, dusk
            $table->boolean('is_focused')->default(false);
            $table->unsignedTinyInteger('focus_order')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedSmallInteger('estimated_minutes')->default(30);
            $table->timestamps();

            $table->index(['is_completed', 'priority']);
            $table->index(['is_focused', 'focus_order']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

