<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import {
    Select, SelectContent, SelectItem,
    SelectTrigger, SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { Save, X } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    form: any;
    errors: Record<string, string>;
    procesando: boolean;
    categorias: Array<{ id: number; nombre: string }>;
}>();

const emit = defineEmits(['submit', 'cancelar']);

const unidades = [
    'UNIDAD', 'CAJA', 'PAQUETE', 'LIBRA', 'KILO',
    'GRAMO', 'LITRO', 'METRO', 'PAR', 'DOCENA',
];

// Precio sin IVA calculado automáticamente
const precioSinIva = computed(() => {
    if (!props.form.aplica_iva) return props.form.precio_venta;
    const pct = props.form.porcentaje_iva || 12;
    return (props.form.precio_venta / (1 + pct / 100)).toFixed(4);
});

const montoIva = computed(() => {
    if (!props.form.aplica_iva) return '0.0000';
    return (props.form.precio_venta - Number(precioSinIva.value)).toFixed(4);
});
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">

        <!-- Identificación -->
        <div>
            <h3 class="text-sm font-medium text-gray-700 mb-3 pb-2 border-b">
                Identificación
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <Label>Código *</Label>
                    <Input v-model="form.codigo" placeholder="PROD-00001" class="font-mono" />
                    <InputError :message="errors.codigo" />
                </div>
                <div class="space-y-1.5">
                    <Label>Código de barras</Label>
                    <Input v-model="form.codigo_barras" placeholder="Optional" class="font-mono" />
                    <InputError :message="errors.codigo_barras" />
                </div>
                <div class="space-y-1.5">
                    <Label>Categoría *</Label>
                    <Select v-model="form.categoria_id">
                        <SelectTrigger>
                            <SelectValue placeholder="Seleccionar..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="cat in categorias"
                                :key="cat.id"
                                :value="String(cat.id)"
                            >
                                {{ cat.nombre }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.categoria_id" />
                </div>
                <div class="space-y-1.5 md:col-span-2">
                    <Label>Nombre del producto *</Label>
                    <Input v-model="form.nombre" placeholder="Nombre descriptivo" />
                    <InputError :message="errors.nombre" />
                </div>
                <div class="space-y-1.5">
                    <Label>Unidad de medida</Label>
                    <Select v-model="form.unidad_medida">
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="u in unidades" :key="u" :value="u">
                                {{ u }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="space-y-1.5 md:col-span-3">
                    <Label>Descripción</Label>
                    <Textarea v-model="form.descripcion" placeholder="Descripción opcional..." rows="2" />
                </div>
            </div>
        </div>

        <!-- Precios -->
        <div>
            <h3 class="text-sm font-medium text-gray-700 mb-3 pb-2 border-b">
                Precios (GTQ)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <Label>Precio de compra (Q)</Label>
                    <Input v-model="form.precio_compra" type="number" min="0" step="0.0001" placeholder="0.00" />
                    <InputError :message="errors.precio_compra" />
                </div>
                <div class="space-y-1.5">
                    <Label>Precio de venta con IVA (Q) *</Label>
                    <Input v-model="form.precio_venta" type="number" min="0" step="0.0001" placeholder="0.00" />
                    <InputError :message="errors.precio_venta" />
                </div>
                <div class="space-y-1.5">
                    <Label>Precio mayoreo (Q)</Label>
                    <Input v-model="form.precio_mayoreo" type="number" min="0" step="0.0001" placeholder="Opcional" />
                </div>
            </div>

            <!-- Desglose IVA -->
            <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                <div class="flex items-center gap-6 text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            v-model="form.aplica_iva"
                            class="w-4 h-4 text-blue-600 rounded"
                        />
                        <span class="font-medium text-blue-800">Aplica IVA 12%</span>
                    </label>
                    <div v-if="form.aplica_iva" class="flex gap-4 text-blue-700">
                        <span>Base: <strong>Q {{ precioSinIva }}</strong></span>
                        <span>IVA: <strong>Q {{ montoIva }}</strong></span>
                    </div>
                    <div v-else class="text-gray-500">
                        Producto exento de IVA
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventario -->
        <div>
            <h3 class="text-sm font-medium text-gray-700 mb-3 pb-2 border-b">
                Inventario
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <Label>Stock actual</Label>
                    <Input v-model="form.stock_actual" type="number" min="0" step="0.0001" placeholder="0" />
                </div>
                <div class="space-y-1.5">
                    <Label>Stock mínimo</Label>
                    <Input v-model="form.stock_minimo" type="number" min="0" step="0.0001" placeholder="0" />
                </div>
                <div class="space-y-1.5">
                    <Label>Tipo</Label>
                    <Select v-model="form.bien_servicio">
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="B">Bien (producto físico)</SelectItem>
                            <SelectItem value="S">Servicio</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Opciones de control -->
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                <label
                    v-for="(label, key) in {
                        activo: 'Activo',
                        se_vende: 'Se vende',
                        se_compra: 'Se compra',
                        controla_inventario: 'Controla inventario',
                    }"
                    :key="key"
                    class="flex items-center gap-2 cursor-pointer p-3 border rounded-lg hover:bg-gray-50"
                >
                    <input
                        type="checkbox"
                        v-model="form[key]"
                        class="w-4 h-4 text-blue-600 rounded"
                    />
                    <span class="text-sm">{{ label }}</span>
                </label>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-3 pt-2">
            <Button type="submit" :disabled="procesando" class="gap-2">
                <Save class="w-4 h-4" />
                {{ procesando ? 'Guardando...' : 'Guardar producto' }}
            </Button>
            <Button type="button" variant="outline" class="gap-2" @click="emit('cancelar')">
                <X class="w-4 h-4" />
                Cancelar
            </Button>
        </div>

    </form>
</template>