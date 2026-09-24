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
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();

            $table->foreignUuid('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('order_id')->constrained()
                ->cascadeOnDelete();

            $table->text('reason');

            $table->unsignedBigInteger('requested_amount_cents');

            $table->string('status')->default('pending');

            $table->string('decision')->nullable();

            $table->string('reason_code')->nullable();

            $table->text('decision_reason')->nullable();

            $table->json('ai_analysis')->nullable();

            $table->string('ai_provider')->nullable();

            $table->string('ai_model')->nullable();
            $table->string('policy_version')->nullable();


            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index([
                'order_id',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
