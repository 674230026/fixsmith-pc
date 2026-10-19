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
        Schema::create('feedback', function (Blueprint $table) {
            $table->string('id')->primary(); // FDB-205, CMP-204
            $table->string('type')->default('Rating'); // 'Rating' or 'Complaint'
            $table->string('repair_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('subject');
            $table->text('message');
            $table->integer('rating')->default(0);
            $table->string('attachment')->nullable();
            $table->string('status')->default('New'); // 'New', 'In review', 'Reviewed', 'Resolved'
            $table->text('admin_reply')->nullable();
            $table->string('age_formatted')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
