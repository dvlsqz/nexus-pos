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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('unit_id')->constrained(); // Obligatorio para saber qué vendemos
            
            $table->string('name');
            $table->string('sku')->nullable(); // Código interno
            $table->string('barcode')->nullable(); // Código de barras
            $table->text('description')->nullable();
            
            $table->decimal('price', 12, 2)->default(0); // Precio de venta
            $table->decimal('cost', 12, 2)->default(0);  // Precio de costo (para calcular utilidad)
            
            $table->boolean('is_active')->default(true);
            $table->boolean('manage_stock')->default(true); // Falso para servicios
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
