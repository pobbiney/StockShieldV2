<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satellite_item_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('satellite_stock_receipt_id');
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->string('batch_number')->nullable();
            $table->unsignedBigInteger('issue_to');
            $table->integer('qty');
            $table->integer('qty_requested')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('requisition_no')->nullable();
            $table->unsignedBigInteger('item_request_id')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->string('invoice_number')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->string('status')->default('pending');
            $table->string('status_two')->default('pending');
            $table->timestamp('received_at')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::table('satellite_stock_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('satellite_stock_receipts', 'satellite_item_issue_id')) {
                $table->unsignedBigInteger('satellite_item_issue_id')->nullable()->after('item_issue_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('satellite_stock_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('satellite_stock_receipts', 'satellite_item_issue_id')) {
                $table->dropColumn('satellite_item_issue_id');
            }
        });

        Schema::dropIfExists('satellite_item_issues');
    }
};
