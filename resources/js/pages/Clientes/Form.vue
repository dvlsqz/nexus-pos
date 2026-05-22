<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { Save, X } from 'lucide-vue-next';

const props = defineProps<{
    form: any;
    errors: Record<string, string>;
    procesando: boolean;
}>();

const emit = defineEmits(['submit', 'cancelar']);

const departamentosGT = [
    'Alta Verapaz', 'Baja Verapaz', 'Chimaltenango', 'Chiquimula',
    'El Progreso', 'Escuintla', 'Guatemala', 'Huehuetenango',
    'Izabal', 'Jalapa', 'Jutiapa', 'Petén', 'Quetzaltenango',
    'Quiché', 'Retalhuleu', 'Sacatepéquez', 'San Marcos',
    'Santa Rosa', 'Sololá', 'Suchitepéquez', 'Totonicapán', 'Zacapa',
];
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">

        <!-- Tipo de cliente -->
        <div class="grid grid-cols-2 gap-1 p-1 bg-gray-100 rounded-lg w-fit">
            <button
                type="button"
                @click="form.tipo = 'NATURAL'"
                class="px-4 py-2 text-sm font-medium rounded-md transition"
                :class="form.tipo === 'NATURAL'
                    ? 'bg-white shadow text-gray-900'
                    : 'text-gray-500 hover:text-gray-700'"
            >
                Persona natural
            </button>
            <button
                type="button"
                @click="form.tipo = 'JURIDICA'"
                class="px-4 py-2 text-sm font-medium rounded-md transition"
                :class="form.tipo === 'JURIDICA'
                    ? 'bg-white shadow text-gray-900'
                    : 'text-gray-500 hover:text-gray-700'"
            >
                Empresa
            </button>
        </div>

        <!-- Datos principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="space-y-1.5">
                <Label>Nombre completo / Razón social *</Label>
                <Input v-model="form.nombre" placeholder="Nombre del cliente" />
                <InputError :message="errors.nombre" />
            </div>

            <div class="space-y-1.5">
                <Label>Nombre comercial</Label>
                <Input v-model="form.nombre_comercial" placeholder="Opcional" />
            </div>

            <div class="space-y-1.5">
                <Label>NIT</Label>
                <Input
                    v-model="form.nit"
                    placeholder="12345678-9 o CF"
                    class="font-mono"
                />
                <InputError :message="errors.nit" />
            </div>

            <div class="space-y-1.5">
                <Label>CUI / DPI</Label>
                <Input
                    v-model="form.cui"
                    placeholder="0000 00000 0000"
                    class="font-mono"
                />
            </div>

        </div>

        <!-- Contacto -->
        <div>
            <h3 class="text-sm font-medium text-gray-700 mb-3 pb-2 border-b">
                Información de contacto
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <Label>Teléfono</Label>
                    <Input v-model="form.telefono" placeholder="2222-3333" />
                </div>
                <div class="space-y-1.5">
                    <Label>WhatsApp</Label>
                    <Input v-model="form.whatsapp" placeholder="5555-6666" />
                </div>
                <div class="space-y-1.5">
                    <Label>Correo electrónico</Label>
                    <Input v-model="form.email" type="email" placeholder="correo@ejemplo.com" />
                    <InputError :message="errors.email" />
                </div>
            </div>
        </div>

        <!-- Dirección -->
        <div>
            <h3 class="text-sm font-medium text-gray-700 mb-3 pb-2 border-b">
                Dirección
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <Label>Departamento</Label>
                    <Select v-model="form.departamento">
                        <SelectTrigger>
                            <SelectValue placeholder="Seleccionar..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="dep in departamentosGT"
                                :key="dep"
                                :value="dep"
                            >
                                {{ dep }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="space-y-1.5">
                    <Label>Municipio</Label>
                    <Input v-model="form.municipio" placeholder="Municipio" />
                </div>
                <div class="space-y-1.5">
                    <Label>Zona</Label>
                    <Input v-model="form.zona" placeholder="Zona 1" />
                </div>
                <div class="space-y-1.5 md:col-span-3">
                    <Label>Dirección completa</Label>
                    <Input v-model="form.direccion" placeholder="Calle, Avenida, No. de casa..." />
                </div>
            </div>
        </div>

        <!-- Condiciones comerciales -->
        <div>
            <h3 class="text-sm font-medium text-gray-700 mb-3 pb-2 border-b">
                Condiciones comerciales
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <Label>Límite de crédito (Q)</Label>
                    <Input v-model="form.credito_limite" type="number" min="0" step="0.01" placeholder="0.00" />
                </div>
                <div class="space-y-1.5">
                    <Label>Días de crédito</Label>
                    <Input v-model="form.credito_dias" type="number" min="0" placeholder="0" />
                </div>
                <div class="space-y-1.5">
                    <Label>Descuento por defecto (%)</Label>
                    <Input v-model="form.descuento_default" type="number" min="0" max="100" step="0.01" placeholder="0.00" />
                </div>
            </div>
        </div>

        <!-- Notas -->
        <div class="space-y-1.5">
            <Label>Notas internas</Label>
            <Textarea
                v-model="form.notas"
                placeholder="Observaciones sobre el cliente..."
                rows="3"
            />
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-3 pt-2">
            <Button type="submit" :disabled="procesando" class="gap-2">
                <Save class="w-4 h-4" />
                {{ procesando ? 'Guardando...' : 'Guardar cliente' }}
            </Button>
            <Button
                type="button"
                variant="outline"
                class="gap-2"
                @click="emit('cancelar')"
            >
                <X class="w-4 h-4" />
                Cancelar
            </Button>
        </div>

    </form>
</template>