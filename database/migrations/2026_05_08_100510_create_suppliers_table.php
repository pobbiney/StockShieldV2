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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('supplier');
            $table->string('phone');
            $table->string('email');
            $table->string('company');
            $table->string('city');
            $table->string('tin_number');
            $table->string('registration_number');
            $table->string('address');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
