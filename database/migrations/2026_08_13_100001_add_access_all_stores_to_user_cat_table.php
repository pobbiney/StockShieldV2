<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_cat', function (Blueprint $table) {
            $table->boolean('access_all_stores')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('user_cat', function (Blueprint $table) {
            $table->dropColumn('access_all_stores');
        });
    }
};
