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
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
             $table->integer('item_id');
            $table->string('batch_number')->nullable();
            $table->integer('item_store_id');
            $table->integer('qty')->nullable();
            $table->string('requisition_no')->nullable();
     
            $table->integer('stock_id');
            
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
        Schema::dropIfExists('item_requests');
    }
};
