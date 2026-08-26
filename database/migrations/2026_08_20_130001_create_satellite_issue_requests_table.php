<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satellite_issue_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('satellite_stock_receipt_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->string('batch_number')->nullable();
            $table->unsignedInteger('qty_requested');
            $table->unsignedInteger('qty_issued')->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('issue_no')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('ward_id');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satellite_issue_requests');
    }
};
