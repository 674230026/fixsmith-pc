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
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role')->default('customer'); // 'customer', 'technician', 'admin'
            $table->string('title');
            $table->text('message');
            $table->string('icon')->default('tool');
            $table->boolean('upcoming')->default(false);
            $table->string('time_label')->nullable();
            $table->string('target')->default('repairs');
            $table->string('entity_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
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
