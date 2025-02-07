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
        Schema::table('trusted_resources', function (Blueprint $table) {
            $table->string('resource_id');
            $table->foreignId('tracked_account_id')->constrained('tracked_accounts')->onDelete('cascade');
            $table->unique(['tracked_account_id', 'resource_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trusted_resources', function (Blueprint $table) {
            $table->dropForeign(['tracked_account_id']);
            $table->dropColumn('tracked_account_id', 'resource_id');
        });
    }
};
