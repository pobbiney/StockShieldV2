<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('stocks', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('updated_by');
            }
            if (!Schema::hasColumn('stocks', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            if (Schema::hasColumn('stocks', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('stocks', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
        });
    }
};
