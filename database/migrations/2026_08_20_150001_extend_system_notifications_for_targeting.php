<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('system_notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('system_notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('reference_id');
            }
            if (!Schema::hasColumn('system_notifications', 'action_route')) {
                $table->string('action_route')->nullable()->after('read_at');
            }
            if (!Schema::hasColumn('system_notifications', 'action_url')) {
                $table->string('action_url')->nullable()->after('action_route');
            }
            if (!Schema::hasColumn('system_notifications', 'store_id')) {
                $table->unsignedBigInteger('store_id')->nullable()->after('action_url')->index();
            }
            if (!Schema::hasColumn('system_notifications', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('store_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_notifications', function (Blueprint $table) {
            $columns = ['user_id', 'read_at', 'action_route', 'action_url', 'store_id', 'created_by'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('system_notifications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
