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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('user_id')->constrained(); // El vendedor
            $table->foreignId('customer_id')->nullable()->constrained();
            
            $table->string('number')->unique(); // Correlativo (ej. FAC-0001)
            $table->decimal('total', 12, 2);
            $table->string('payment_method')->default('cash'); // cash, card, transfer
            $table->string('status')->default('completed'); // completed, pending, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
