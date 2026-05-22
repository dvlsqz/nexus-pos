<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ProveedorForm from './Form.vue';
import swal from '@/lib/swal';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    proveedor: any;
}>();

const form = useForm({
    nit:                props.proveedor.nit ?? '',
    nombre:             props.proveedor.nombre,
    nombre_comercial:   props.proveedor.nombre_comercial ?? '',
    contacto_nombre:    props.proveedor.contacto_nombre ?? '',
    telefono:           props.proveedor.telefono ?? '',
    telefono_alt:       props.proveedor.telefono_alt ?? '',
    email:              props.proveedor.email ?? '',
    whatsapp:           props.proveedor.whatsapp ?? '',
    website:            props.proveedor.website ?? '',
    pais:               props.proveedor.pais ?? 'Guatemala',
    departamento:       props.proveedor.departamento ?? '',
    municipio:          props.proveedor.municipio ?? '',
    direccion:          props.proveedor.direccion ?? '',
    credito_dias:       props.proveedor.credito_dias,
    descuento_default:  props.proveedor.descuento_default,
    moneda:             props.proveedor.moneda,
    activo:             props.proveedor.activo,
    notas:              props.proveedor.notas ?? '',
});

function guardar() {
    form.put(`/proveedores/${props.proveedor.id}`, {
        onSuccess: () => {
            swal.fire({
                title: '¡Actualizado!',
                text: 'El proveedor fue actualizado correctamente.',
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
    <Head title="Editar Proveedor" />

    <div class="p-6 max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Editar proveedor</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ proveedor.nombre }}</p>
            </div>

            <button
                type="button"
                @click="form.activo = !form.activo"
                class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium transition"
                :class="form.activo
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700'"
            >
                <span class="w-2 h-2 rounded-full"
                      :class="form.activo ? 'bg-green-500' : 'bg-red-500'" />
                {{ form.activo ? 'Activo' : 'Inactivo' }}
            </button>
        </div>

        <div class="bg-white border rounded-lg p-6">
            <ProveedorForm
                :form="form"
                :errors="form.errors"
                :procesando="form.processing"
                @submit="guardar"
                @cancelar="router.visit('/proveedores')"
            />
        </div>
    </div>
</template>