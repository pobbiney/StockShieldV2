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
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->string('batch_number');
            $table->integer('quantity');
            $table->string('manager_comment')->nullable();
            $table->string('admin_comment')->nullable();
            $table->string('hod_comment')->nullable();
            $table->integer('returned_by');
            $table->integer('approved_by_admin')->nullable();
            $table->integer('approved_by_hod')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
