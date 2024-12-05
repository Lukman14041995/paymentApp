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
        Schema::create('sessions', function (Blueprint $table) {

            $table->id();  // kolom primary key
            $table->unsignedBigInteger('user_id');  // Menggunakan unsignedBigInteger untuk user_id
            $table->string('ip_address');  // Menggunakan string untuk IP address
            $table->text('user_agent');  // Menggunakan text untuk user agent
            $table->text('payload');  // Menggunakan text untuk payload
            $table->integer('last_activity');  // Menggunakan integer untuk timestamp
            $table->timestamp('expired_activity')->nullable();  // Menggunakan timestamp untuk expired_activity, nullable jika tidak selalu ada
            $table->timestamps();  // Timestamps untuk created_at dan updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
