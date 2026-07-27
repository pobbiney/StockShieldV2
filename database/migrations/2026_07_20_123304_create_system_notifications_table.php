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
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
             $table->string('title');
        $table->string('message');
        $table->string('link')->nullable();
        $table->string('type'); // e.g. 'new_request', 'pending_issue'
        $table->unsignedBigInteger('reference_id')->nullable(); // e.g. item_request id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
