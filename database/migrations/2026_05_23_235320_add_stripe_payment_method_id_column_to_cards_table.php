<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('stripe_payment_method_id')->nullable()->after('card_id');
        });
    }

    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
        });
    }
};
