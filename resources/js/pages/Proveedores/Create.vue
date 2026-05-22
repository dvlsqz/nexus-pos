<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ProveedorForm from './Form.vue';
import swal from '@/lib/swal';

defineOptions({ layout: AppLayout });

const form = useForm({
    nit:                '',
    nombre:             '',
    nombre_comercial:   '',
    contacto_nombre:    '',
    telefono:           '',
    telefono_alt:       '',
    email:              '',
    whatsapp:           '',
    website:            '',
    pais:               'Guatemala',
    departamento:       '',
    municipio:          '',
    direccion:          '',
    credito_dias:       0,
    descuento_default:  0,
    moneda:             'GTQ',
    notas:              '',
});

function guardar() {
    form.post('/proveedores', {
        onSuccess: () => {
            swal.fire({
                title: '¡Proveedor creado!',
                text: 'El proveedor fue registrado correctamente.',
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
    <Head title="Nuevo Proveedor" />

    <div class="p-6 max-w-4xl space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Nuevo proveedor</h1>
            <p class="text-sm text-gray-500 mt-0.5">Completa los datos del proveedor</p>
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