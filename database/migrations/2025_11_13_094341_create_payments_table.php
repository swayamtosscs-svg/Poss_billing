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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->enum('type', ['in', 'out']); // payment in (received) or payment out (paid)
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('set null');
            $table->morphs('paymentable'); // polymorphic relation (sale, purchase, expense)
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // cash, bank, upi, card, cheque
            $table->unsignedBigInteger('bank_account_id')->nullable(); // will add foreign key after bank_accounts created
            $table->string('reference_number')->nullable(); // cheque no, transaction id
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
