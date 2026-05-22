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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            $table->string('numero', 30)->unique();
            $table->date('fecha');

            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();

            // Totales GTQ
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento_total', 12, 2)->default(0);
            $table->decimal('base_imponible', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('exento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Pago
            $table->enum('tipo_pago', ['CONTADO', 'CREDITO'])->default('CONTADO');
            $table->string('metodo_pago', 30)->default('EFECTIVO');
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->decimal('cambio', 12, 2)->default(0);

            // Estado
            $table->enum('estado', ['COMPLETADA', 'ANULADA'])->default('COMPLETADA');
            $table->text('motivo_anulacion')->nullable();
            $table->timestamp('anulada_at')->nullable();
            $table->foreignId('anulada_por')->nullable()->constrained('users')->nullOnDelete();

            // Tipo documento
            $table->enum('tipo_documento', ['COMPROBANTE', 'FACTURA'])->default('COMPROBANTE');

            // FEL preparado
            $table->string('fel_uuid', 100)->nullable();
            $table->string('fel_serie', 20)->nullable();
            $table->string('fel_numero', 20)->nullable();

            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['fecha', 'estado']);
            $table->index('cliente_id');
        });

        Schema::create('venta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();

            $table->string('descripcion', 300);
            $table->decimal('cantidad', 12, 4);
            $table->decimal('precio_unitario', 12, 4);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_monto', 12, 4)->default(0);

            $table->boolean('aplica_iva')->default(true);
            $table->decimal('porcentaje_iva', 5, 2)->default(12.00);
            $table->decimal('monto_iva', 12, 4)->default(0);

            $table->decimal('subtotal', 12, 4);
            $table->decimal('total', 12, 4);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_items');
        Schema::dropIfExists('ventas');
    }
};
