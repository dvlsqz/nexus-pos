<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ClienteForm from './Form.vue';
import { Button } from '@/components/ui/button';
import swal from '@/lib/swal';
defineOptions({ layout: AppLayout });

const props = defineProps<{
    cliente: {
        id: number;
        tipo: string;
        nit: string;
        cui: string;
        nombre: string;
        nombre_comercial: string;
        telefono: string;
        email: string;
        whatsapp: string;
        departamento: string;
        municipio: string;
        zona: string;
        direccion: string;
        credito_limite: number;
        credito_dias: number;
        descuento_default: number;
        activo: boolean;
        notas: string;
    };
}>();

const form = useForm({
    tipo:              props.cliente.tipo,
    nit:               props.cliente.nit ?? '',
    cui:               props.cliente.cui ?? '',
    nombre:            props.cliente.nombre,
    nombre_comercial:  props.cliente.nombre_comercial ?? '',
    telefono:          props.cliente.telefono ?? '',
    email:             props.cliente.email ?? '',
    whatsapp:          props.cliente.whatsapp ?? '',
    departamento:      props.cliente.departamento ?? '',
    municipio:         props.cliente.municipio ?? '',
    zona:              props.cliente.zona ?? '',
    direccion:         props.cliente.direccion ?? '',
    credito_limite:    props.cliente.credito_limite,
    credito_dias:      props.cliente.credito_dias,
    descuento_default: props.cliente.descuento_default,
    activo:            props.cliente.activo,
    notas:             props.cliente.notas ?? '',
});

function guardar() {
    form.put(`/clientes/${props.cliente.id}`, {
        onSuccess: () => {
            swal.fire({
                title: '¡Actualizado!',
                text: 'El cliente fue actualizado correctamente.',
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
    <Head title="Editar Cliente" />

    <div class="p-6 max-w-4xl space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Editar cliente
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ cliente.nombre }}
                </p>
            </div>

            <!-- Toggle activo/inactivo -->
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Estado:</span>
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