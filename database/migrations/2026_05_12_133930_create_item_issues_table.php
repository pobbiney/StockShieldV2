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
        Schema::create('item_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->string('batch_number');
            $table->integer('issue_to');
            $table->integer('qty');
            $table->string('requisition_no');
            $table->string('store_id');
            $table->integer('stock_id');
            $table->string('invoice_number')->nullable();
            $table->integer('created_by');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_issues');
    }
};
