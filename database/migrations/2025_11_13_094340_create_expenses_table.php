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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->onDelete('cascade');
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('set null');
            $table->string('expense_number')->unique();
            $table->date('expense_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable(); // will add foreign key after bank_accounts created
            $table->text('description')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
