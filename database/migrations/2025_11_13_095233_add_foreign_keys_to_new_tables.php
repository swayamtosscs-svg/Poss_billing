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
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
        });
    }
};
