<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ProductoForm from './Form.vue';
import swal from '@/lib/swal';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    categorias: Array<{ id: number; nombre: string }>;
    codigoSugerido: string;
}>();

const form = useForm({
    categoria_id:        '',
    codigo:              props.codigoSugerido,
    codigo_barras:       '',
    nombre:              '',
    descripcion:         '',
    unidad_medida:       'UNIDAD',
    precio_compra:       0,
    precio_venta:        0,
    precio_mayoreo:      null,
    aplica_iva:          true,
    porcentaje_iva:      12,
    stock_actual:        0,
    stock_minimo:        0,
    activo:              true,
    se_vende:            true,
    se_compra:           true,
    controla_inventario: true,
    bien_servicio:       'B',
});

function guardar() {
    form.post('/productos', {
        onSuccess: () => {
            swal.fire({
                title: '¡Producto creado!',
                text: 'El producto fue registrado correctamente.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        },
        onError: () => {
            swal.fire({
                title: 'Error de validación',
                text: 'Revisa los campos marcados en rojo.',
                icon: 'error',
                confirmButtonColor: '#3b82f6',
            });
        },
    });
}
</script>

<template>
    <Head title="Nuevo Producto" />

    <div class="p-6 max-w-4xl space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Nuevo producto</h1>
            <p class="text-sm text-gray-500 mt-0.5">Completa los datos del producto</p>
        </div>

        <div class="bg-white border rounded-lg p-6">
            <ProductoForm
                :form="form"
                :errors="form.errors"
                :procesando="form.processing"
                :categorias="categorias"
                @submit="guardar"
                @cancelar="router.visit('/productos')"
            />
        </div>
    </div>
</template>