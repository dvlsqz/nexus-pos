<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select, SelectContent, SelectItem,
    SelectTrigger, SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { Save, X } from 'lucide-vue-next';
import swal from '@/lib/swal';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    usuario: {
        id: number;
        name: string;
        email: string;
        activo: boolean;
        roles: Array<{ name: string }>;
    };
    roles: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    name:     props.usuario.name,
    email:    props.usuario.email,
    password: '',
    rol:      props.usuario.roles[0]?.name ?? '',
    activo:   props.usuario.activo,
});

function guardar() {
    form.put(`/usuarios/${props.usuario.id}`, {
        onSuccess: () => {
            swal.fire({
                title: '¡Actualizado!',
                text: 'El usuario fue actualizado correctamente.',
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
    <Head title="Editar Usuario" />

    <div class="p-6 max-w-2xl space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Editar usuario</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ usuario.name }}</p>
            </div>

            <!-- Toggle activo -->
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
            <form @submit.prevent="guardar" class="space-y-5">

                <div class="space-y-1.5">
                    <Label>Nombre completo *</Label>
                    <Input v-model="form.name" placeholder="Nombre del usuario" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-1.5">
                    <Label>Correo electrónico *</Label>
                    <Input v-model="form.email" type="email" placeholder="correo@empresa.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-1.5">
                    <Label>Nueva contraseña</Label>
                    <Input
                        v-model="form.password"
                        type="password"
                        placeholder="Dejar vacío para no cambiar"
                    />
                    <p class="text-xs text-gray-400">
                        Solo completa este campo si deseas cambiar la contraseña.
                    </p>
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-1.5">
                    <Label>Rol *</Label>
                    <Select v-model="form.rol">
                        <SelectTrigger>
                            <SelectValue placeholder="Seleccionar rol..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="r in roles"
                                :key="r.id"
                                :value="r.name"
                            >
                                {{ r.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.rol" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <Button type="submit" :disabled="form.processing" class="gap-2">
                        <Save class="w-4 h-4" />
                        {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        class="gap-2"
                        @click="router.visit('/usuarios')"
                    >
                        <X class="w-4 h-4" />
                        Cancelar
                    </Button>
                </div>

            </form>
        </div>
    </div>
</template>