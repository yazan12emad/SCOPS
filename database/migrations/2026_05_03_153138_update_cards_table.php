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
        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();

            $table->tinyInteger('expiry_month')->change();
            $table->smallInteger('expiry_year')->change();

            $table->unique('tokenized_pan');
            $table->index('user_id');
            $table->string('last4', 4)->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropUnique(['tokenized_pan']);
            $table->dropIndex(['user_id']);
            $table->dropForeign(['user_id']);
            $table->integer('expiry_month')->change();
            $table->integer('expiry_year')->change();
            $table->string('last4')->change();
        });
    }
};
