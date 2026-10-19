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
        Schema::create('repairs', function (Blueprint $table) {
            $table->string('id')->primary(); // FX-1048
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('device');
            $table->string('serial');
            $table->text('issue');
            $table->string('method')->default('Mail-in'); // 'Mail-in' or 'Drop-off'
            $table->string('status')->default('Pending'); // 'Package received', 'Diagnosing', 'Waiting for part', 'Fixing', 'Pending', 'Finished'
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('technician_name')->nullable();
            $table->integer('active_step')->default(0);
            $table->text('latest_update')->nullable();
            $table->string('device_photo')->nullable();
            $table->string('shipping_slip')->nullable();
            $table->boolean('interacted')->default(false);
            $table->string('created_date_formatted')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
