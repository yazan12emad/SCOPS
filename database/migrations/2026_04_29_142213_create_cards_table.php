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
        Schema::create('cards', function (Blueprint $table) {
            $table->id('card_id');

            $table->foreignId('user_id')->constrained('users', 'user_id');

            $table->string('card_holder_name'); // name of the owner
            $table->string('card_brand'); // type or card (VISA, MasterCard, etc.)
            $table->string('last4'); // last 4 digits of the card number
            $table->integer('expiry_month'); // 1-12
            $table->integer('expiry_year');
            $table->boolean('is_primary')->default(false); // The main payment card
            $table->string('tokenized_pan'); // Token returned by payment gateway instead of real card number

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
