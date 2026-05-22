<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ProductoForm from './Form.vue';
import swal from '@/lib/swal';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    producto: any;
    categorias: Array<{ id: number; nombre: string }>;
}>();

const form = useForm({
    categoria_id:        String(props.producto.categoria_id),
    codigo:              props.producto.codigo,
    codigo_barras:       props.producto.codigo_barras ?? '',
    nombre:              props.producto.nombre,
    descripcion:         props.producto.descripcion ?? '',
    unidad_medida:       props.producto.unidad_medida,
    precio_compra:       props.producto.precio_compra,
    precio_venta:        props.producto.precio_venta,
    precio_mayoreo:      props.producto.precio_mayoreo ?? null,
    aplica_iva:          props.producto.aplica_iva,
    porcentaje_iva:      props.producto.porcentaje_iva,
    stock_actual:        props.producto.stock_actual,
    stock_minimo:        props.producto.stock_minimo,
    activo:              props.producto.activo,
    se_vende:            props.producto.se_vende,
    se_compra:           props.producto.se_compra,
    controla_inventario: props.producto.controla_inventario,
    bien_servicio:       props.producto.bien_servicio,
});

function guardar() {
    form.put(`/productos/${props.producto.id}`, {
        onSuccess: () => {
            swal.fire({
                title: '¡Actualizado!',
                text: 'El producto fue actualizado correctamente.',
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
    <Head title="Editar Producto" />

    <div class="p-6 max-w-4xl space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Editar producto</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ producto.nombre }}</p>
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