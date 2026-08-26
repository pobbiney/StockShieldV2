<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_extra_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('link_id');
            $table->timestamps();

            $table->unique(['user_id', 'link_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_extra_links');
    }
};
