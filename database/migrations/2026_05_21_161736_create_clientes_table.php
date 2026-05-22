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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->enum('tipo', ['NATURAL', 'JURIDICA'])->default('NATURAL');

            // Identificación Guatemala
            $table->string('nit', 20)->nullable();
            $table->string('cui', 15)->nullable();
            $table->string('nombre', 200);
            $table->string('nombre_comercial', 200)->nullable();

            // Contacto
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('whatsapp', 20)->nullable();

            // Dirección Guatemala
            $table->string('departamento', 100)->nullable();
            $table->string('municipio', 100)->nullable();
            $table->string('zona', 10)->nullable();
            $table->text('direccion')->nullable();

            // Comercial
            $table->decimal('credito_limite', 12, 2)->default(0);
            $table->integer('credito_dias')->default(0);
            $table->decimal('descuento_default', 5, 2)->default(0);

            $table->boolean('activo')->default(true);
            $table->text('notas')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('nit');
            $table->index('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
