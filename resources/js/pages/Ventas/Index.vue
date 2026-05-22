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
import { Search, ShoppingCart, Eye } from 'lucide-vue-next';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    ventas: {
        data: Array<{
            id: number;
            numero: string;
            fecha: string;
            cliente: { nombre: string };
            user: { name: string };
            total: number;
            metodo_pago: string;
            tipo_documento: string;
            estado: string;
        }>;
        links: Array<{ url: string; label: string; active: boolean }>;
        total: number;
    };
    filtros: { buscar?: string; estado?: string };
}>();

const buscar = ref(props.filtros.buscar ?? '');

let timeout: ReturnType<typeof setTimeout>;
watch(buscar, (valor) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/ventas', { buscar: valor }, {
            preserveState: true, replace: true,
        });
    }, 400);
});

function formatGTQ(monto: number): string {
    return 'Q ' + Number(monto).toLocaleString('es-GT', {
        minimumFractionDigits: 2, maximumFractionDigits: 2,
    });
}

function formatFecha(fecha: string): string {
    return new Date(fecha).toLocaleDateString('es-GT');
}
</script>

<template>
    <Head title="Ventas" />

    <div class="p-6 space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Ventas</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ ventas.total }} ventas registradas
                </p>
            </div>
            <Link href="/ventas/pos">
                <Button class="gap-2">
                    <ShoppingCart class="w-4 h-4" />
                    Nueva venta
                </Button>
            </Link>
        </div>

        <div class="relative max-w-sm">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <Input v-model="buscar" placeholder="Buscar por número o cliente..." class="pl-9" />
        </div>

        <div class="border rounded-lg overflow-hidden">
            <Table>
                <TableHeader>
                    <TableRow class="bg-gray-50">
                        <TableHead>Número</TableHead>
                        <TableHead>Fecha</TableHead>
                        <TableHead>Cliente</TableHead>
                        <TableHead>Vendedor</TableHead>
                        <TableHead>Documento</TableHead>
                        <TableHead>Método</TableHead>
                        <TableHead>Total</TableHead>
                        <TableHead>Estado</TableHead>
                        <TableHead class="text-right">Acciones</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="venta in ventas.data"
                        :key="venta.id"
                        class="hover:bg-gray-50"
                    >
                        <TableCell class="font-mono text-sm font-medium">
                            {{ venta.numero }}
                        </TableCell>
                        <TableCell class="text-sm text-gray-600">
                            {{ formatFecha(venta.fecha) }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ venta.cliente.nombre }}
                        </TableCell>
                        <TableCell class="text-sm text-gray-500">
                            {{ venta.user.name }}
                        </TableCell>
                        <TableCell>
                            <Badge class="text-xs bg-blue-100 text-blue-700">
                                {{ venta.tipo_documento }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-sm text-gray-500">
                            {{ venta.metodo_pago }}
                        </TableCell>
                        <TableCell class="font-bold text-gray-900">
                            {{ formatGTQ(venta.total) }}
                        </TableCell>
                        <TableCell>
                            <Badge
                                :class="venta.estado === 'COMPLETADA'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'"
                                class="text-xs"
                            >
                                {{ venta.estado }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-right">
                            <Link :href="`/ventas/${venta.id}`">
                                <Button variant="ghost" size="sm" class="gap-1">
                                    <Eye class="w-3.5 h-3.5" />
                                    Ver
                                </Button>
                            </Link>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="ventas.data.length === 0">
                        <TableCell colspan="9" class="text-center py-12 text-gray-400">
                            No se encontraron ventas
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div class="flex items-center gap-1">
            <template v-for="link in ventas.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="px-3 py-1.5 text-sm rounded border transition"
                    :class="link.active
                        ? 'bg-primary text-white border-primary'
                        : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                    v-html="link.label"
                />
                <span v-else class="px-3 py-1.5 text-sm text-gray-300" v-html="link.label" />
            </template>
        </div>

    </div>
</template>