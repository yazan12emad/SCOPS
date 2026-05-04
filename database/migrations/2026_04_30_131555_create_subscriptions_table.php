<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('card_id');
            $table->foreignId('plan_id')->constrained('service_plans')->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->string('billing_cycle');
            $table->date('start_date');
            $table->date('renewal_date');
            $table->string('status')->default('active');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('card_id')->references('card_id')->on('card')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
