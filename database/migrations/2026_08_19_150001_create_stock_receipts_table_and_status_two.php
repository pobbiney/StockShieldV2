<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_issue_id')->unique();
            $table->unsignedBigInteger('item_request_id')->nullable();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->string('batch_number')->nullable();
            $table->integer('qty');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('purchase_order')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('central_store_id')->nullable();
            $table->string('requisition_no')->nullable();
            $table->string('invoice_number')->nullable();
            $table->unsignedBigInteger('received_by');
            $table->timestamp('received_at');
            $table->timestamps();
        });

        Schema::table('item_issues', function (Blueprint $table) {
            if (!Schema::hasColumn('item_issues', 'status_two')) {
                $table->string('status_two')->default('issued')->after('status');
            }
        });

        DB::table('item_issues')->where('status', 'pending')->update(['status_two' => 'pending']);
        DB::table('item_issues')->where('status', 'issued')->update(['status_two' => 'issued']);
        DB::table('item_issues')->where('status', 'received')->update(['status_two' => 'received']);
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_receipts');

        Schema::table('item_issues', function (Blueprint $table) {
            if (Schema::hasColumn('item_issues', 'status_two')) {
                $table->dropColumn('status_two');
            }
        });
    }
};
