<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id')->unique();
            $table->string('name');
            $table->string('ghana_card_number')->index();
            $table->string('phone_number')->index();
            $table->string('pin');
            $table->string('type')->default("user");
            $table->string('status')->default("created");
            $table->string('kyc_status')->default("queued");
            $table->bigInteger('actual_balance')->default(0);
            $table->bigInteger('available_balance')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
