<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_reversals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('batch_number');
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedBigInteger('approve_stock_id')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->string('reversal_type');
            $table->unsignedInteger('qty_to_reverse')->default(0);
            $table->text('reason');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('approval_comment')->nullable();
            $table->timestamps();

            $table->index(['batch_number', 'status']);
            $table->index(['store_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reversals');
    }
};
