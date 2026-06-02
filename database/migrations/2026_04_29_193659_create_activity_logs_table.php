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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('user_id')->nullable(); // nullable for system/CLI actors
            $table->string('actor_type', 50)->default('user'); // user | system | api
            $table->string('action_type', 50);                 // created | updated | deleted
            $table->string('entity_type', 100);                // App\Models\Order
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('meta_json')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']); // fast lookup: history of one record
            $table->index(['user_id', 'created_at']);     // fast lookup: what a user did over time
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
