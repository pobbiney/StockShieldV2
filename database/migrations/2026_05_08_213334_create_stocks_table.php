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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->string('batch_number')->nullable();
            $table->string('manufacturing_date')->nullable();
            $table->string('expiry_date');
            $table->string('supplier_id');
            $table->string('purchase_order')->nullable();
            $table->string('waybill');
            $table->string('award_letter');
            $table->double('amount');
            $table->integer('store_id');
            $table->string('barcode')->nullable();
            $table->string('barcode_path')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
