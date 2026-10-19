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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('role')->default('customer')->after('phone'); // customer, technician, admin
            $table->string('avatar_initials', 10)->nullable()->after('role');
            $table->boolean('active')->default(true)->after('avatar_initials');
            $table->integer('technician_capacity')->default(3)->after('active');
            $table->string('technician_title')->nullable()->after('technician_capacity');
            $table->string('bio_meta')->nullable()->after('technician_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'role',
                'avatar_initials',
                'active',
                'technician_capacity',
                'technician_title',
                'bio_meta',
            ]);
        });
    }
};
