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
        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mailer')->default('smtp'); // 'smtp' or 'log'
            $table->string('host')->nullable()->default('smtp.mailtrap.io');
            $table->integer('port')->default(587);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('encryption')->nullable()->default('tls'); // 'tls', 'ssl', or null
            $table->string('from_address')->default('support@fixsmith.com');
            $table->string('from_name')->default('Fixsmith Repair Studio');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smtp_settings');
    }
};
