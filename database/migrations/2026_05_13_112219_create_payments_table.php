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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('subscription_id');
            $table->timestamp('attempted_on');
            $table->decimal('amount' , 10 , 2);
            $table->enum('status', ['successful', 'failed', 'pending']);
            $table->String('gateway_reference')->nullable();
            $table->integer('attempt_count')->default(1);
            $table->String('receipt_url')->nullable();
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('subscriptions');

            $table->index('subscription_id');

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
