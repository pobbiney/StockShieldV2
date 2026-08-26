<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_stock_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('batch_number')->nullable();
            $table->string('manufacturing_date')->nullable();
            $table->string('expiry_date')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->string('purchase_order')->nullable();
            $table->string('waybill');
            $table->string('award_letter');
            $table->decimal('amount', 10, 2)->default(0);
            $table->integer('qty');
            $table->unsignedBigInteger('store_id');
            $table->string('barcode')->nullable();
            $table->string('barcode_path')->nullable();
            $table->longText('comment')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->longText('rejection_reason')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_stock_entries');
    }
};
