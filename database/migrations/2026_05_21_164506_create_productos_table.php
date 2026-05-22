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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')
                  ->constrained('categorias_productos')
                  ->restrictOnDelete();

            $table->string('codigo', 50)->unique();
            $table->string('codigo_barras', 100)->nullable()->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->string('imagen', 500)->nullable();
            $table->string('unidad_medida', 30)->default('UNIDAD');

            // Precios en GTQ
            $table->decimal('precio_compra', 12, 4)->default(0);
            $table->decimal('precio_venta', 12, 4);
            $table->decimal('precio_mayoreo', 12, 4)->nullable();

            // IVA
            $table->boolean('aplica_iva')->default(true);
            $table->decimal('porcentaje_iva', 5, 2)->default(12.00);

            // Stock
            $table->decimal('stock_actual', 12, 4)->default(0);
            $table->decimal('stock_minimo', 12, 4)->default(0);

            // Control
            $table->boolean('activo')->default(true);
            $table->boolean('se_vende')->default(true);
            $table->boolean('se_compra')->default(true);
            $table->boolean('controla_inventario')->default(true);

            // FEL preparado
            $table->string('bien_servicio', 1)->default('B');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['codigo', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
