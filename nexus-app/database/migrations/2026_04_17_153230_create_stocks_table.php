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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            // Conexión triple: Producto + Sucursal + Empresa
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            
            // La cantidad disponible (usamos decimal para permitir 0.5 kg, 1.25 mts, etc.)
            $table->decimal('quantity', 12, 3)->default(0);
            
            // Alerta de stock mínimo personalizada por sucursal
            $table->decimal('min_stock', 12, 3)->default(0);
            
            $table->timestamps();
            
            // Seguridad: No permitir duplicar la fila de un producto en la misma sucursal
            $table->unique(['product_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
