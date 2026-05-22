<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Table, TableBody, TableCell,
    TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import {
    Select, SelectContent, SelectItem,
    SelectTrigger, SelectValue,
} from '@/components/ui/select';
import { Plus, Search, Pencil, Trash2, AlertTriangle } from 'lucide-vue-next';
import swal from '@/lib/swal';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    productos: {
        data: Array<{
            id: number;
            codigo: string;
            nombre: string;
            categoria: { nombre: string; color: string };
            precio_venta: number;
            stock_actual: number;
            stock_minimo: number;
            aplica_iva: boolean;
            activo: boolean;
            unidad_medida: string;
        }>;
        links: Array<{ url: string; label: string; active: boolean }>;
        total: number;
    };
    categorias: Array<{ id: number; nombre: string; color: string }>;
    filtros: { buscar?: string; categoria_id?: string; estado?: string };
}>();

const buscar     = ref(props.filtros.buscar ?? '');
const categoriaId = ref(props.filtros.categoria_id ?? 'all');

let timeout: ReturnType<typeof setTimeout>;

watch(buscar, (valor) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/productos', {
            buscar: valor,
            categoria_id: categoriaId.value,
        }, { preserveState: true, replace: true });
    }, 400);
});

watch(categoriaId, (valor) => {
    router.get('/productos', {
        buscar: buscar.value,
        categoria_id: valor === 'all' ? '' : valor,
    }, { preserveState: true, replace: true });
});

function formatGTQ(monto: number): string {
    return 'Q ' + Number(monto).toLocaleString('es-GT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

async function eliminar(id: number, nombre: string) {
    const result = await swal.fire({
        title: '¿Eliminar producto?',
        text: `Se eliminará "${nombre}" del sistema.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(`/productos/${id}`, {
            onSuccess: () => {
                swal.fire({
                    title: '¡Eliminado!',
                    text: `${nombre} fue eliminado correctamente.`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        });
    }
}
</script>

<template>
    <Head title="Productos" />

    <div class="p-6 space-y-6">

        <!-- Encabezado -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Productos</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ productos.total }} productos registrados
                </p>
            </div>
            <Link href="/productos/create">
                <Button class="gap-2">
                    <Plus class="w-4 h-4" />
                    Nuevo producto
                </Button>
            </Link>
        </div>

        <!-- Filtros -->
        <div class="flex gap-3 flex-wrap">
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <Input
                    v-model="buscar"
                    placeholder="Buscar por nombre o código..."
                    class="pl-9"
                />
            </div>
            <Select v-model="categoriaId">
                <SelectTrigger class="w-48">
                    <SelectValue placeholder="Todas las categorías" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">Todas las categorías</SelectItem>
                    <SelectItem
                        v-for="cat in categorias"
                        :key="cat.id"
                        :value="String(cat.id)"
                    >
                        {{ cat.nombre }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>

        <!-- Tabla -->
        <div class="border rounded-lg overflow-hidden">
            <Table>
                <TableHeader>
                    <TableRow class="bg-gray-50">
                        <TableHead>Código</TableHead>
                        <TableHead>Producto</TableHead>
                        <TableHead>Categoría</TableHead>
                        <TableHead>Precio venta</TableHead>
                        <TableHead>Stock</TableHead>
                        <TableHead>IVA</TableHead>
                        <TableHead>Estado</TableHead>
                        <TableHead class="text-right">Acciones</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="producto in productos.data"
                        :key="producto.id"
                        class="hover:bg-gray-50"
                    >
                        <TableCell class="font-mono text-sm text-gray-500">
                            {{ producto.codigo }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ producto.nombre }}
                            <span class="text-xs text-gray-400 ml-1">
                                {{ producto.unidad_medida }}
                            </span>
                        </TableCell>
                        <TableCell>
                            <span
                                class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded-full font-medium"
                                :style="{
                                    backgroundColor: producto.categoria.color + '20',
                                    color: producto.categoria.color,
                                }"
                            >
                                <span
                                    class="w-1.5 h-1.5 rounded-full"
                                    :style="{ backgroundColor: producto.categoria.color }"
                                />
                                {{ producto.categoria.nombre }}
                            </span>
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ formatGTQ(producto.precio_venta) }}
                        </TableCell>
                        <TableCell>
                            <div class="flex items-center gap-1.5">
                                <AlertTriangle
                                    v-if="producto.stock_actual <= producto.stock_minimo"
                                    class="w-3.5 h-3.5 text-amber-500"
                                />
                                <span
                                    :class="producto.stock_actual <= producto.stock_minimo
                                        ? 'text-amber-600 font-medium'
                                        : 'text-gray-700'"
                                >
                                    {{ producto.stock_actual }}
                                </span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <Badge
                                :class="producto.aplica_iva
                                    ? 'bg-blue-100 text-blue-700'
                                    : 'bg-gray-100 text-gray-600'"
                                class="text-xs"
                            >
                                {{ producto.aplica_iva ? '12%' : 'Exento' }}
                            </Badge>
                        </TableCell>
                        <TableCell>
                            <Badge
                                :class="producto.activo
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'"
                                class="text-xs"
                            >
                                {{ producto.activo ? 'Activo' : 'Inactivo' }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="`/productos/${producto.id}/edit`">
                                    <Button variant="ghost" size="sm" class="gap-1">
                                        <Pencil class="w-3.5 h-3.5" />
                                        Editar
                                    </Button>
                                </Link>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="gap-1 text-red-600 hover:text-red-700 hover:bg-red-50"
                                    @click="eliminar(producto.id, producto.nombre)"
                                >
                                    <Trash2 class="w-3.5 h-3.5" />
                                    Eliminar
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="productos.data.length === 0">
                        <TableCell colspan="8" class="text-center py-12 text-gray-400">
                            No se encontraron productos
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Paginación -->
        <div class="flex items-center gap-1">
            <template v-for="link in productos.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="px-3 py-1.5 text-sm rounded border transition"
                    :class="link.active
                        ? 'bg-primary text-white border-primary'
                        : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                    v-html="link.label"
                />
                <span
                    v-else
                    class="px-3 py-1.5 text-sm text-gray-300"
                    v-html="link.label"
                />
            </template>
        </div>

    </div>
</template>