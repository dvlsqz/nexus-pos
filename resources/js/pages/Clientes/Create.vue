<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import ClienteForm from './Form.vue';
import swal from '@/lib/swal'; 
defineOptions({ layout: AppLayout });

const form = useForm({
    tipo: 'NATURAL',
    nit: '',
    cui: '',
    nombre: '',
    nombre_comercial: '',
    telefono: '',
    email: '',
    whatsapp: '',
    departamento: '',
    municipio: '',
    zona: '',
    direccion: '',
    credito_limite: 0,
    credito_dias: 0,
    descuento_default: 0,
    notas: '',
});

function guardar() {
    form.post('/clientes', {
        onSuccess: () => {
            swal.fire({
                title: '¡Cliente creado!',
                text: 'El cliente fue registrado correctamente.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        },
        onError: () => {
            swal.fire({
                title: 'Error',
                text: 'Revisa los campos del formulario.',
                icon: 'error',
                confirmButtonColor: '#3b82f6',
            });
        },
    });
}
</script>

<template>
    <Head title="Nuevo Cliente" />

    <div class="p-6 max-w-4xl space-y-6">

        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Nuevo cliente</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Completa los datos del cliente
            </p>
        </div>

        <div class="bg-white border rounded-lg p-6">
            <ClienteForm
                :form="form"
                :errors="form.errors"
                :procesando="form.processing"
                @submit="guardar"
                @cancelar="router.visit('/clientes')"
            />
        </div>

    </div>
</template>