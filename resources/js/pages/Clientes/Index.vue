<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import swal from '@/lib/swal';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Plus, Search, Pencil, Trash2, Phone, Mail } from 'lucide-vue-next';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    clientes: {
        data: Array<{
            id: number;
            nombre: string;
            nit: string;
            tipo: string;
            telefono: string;
            email: string;
            departamento: string;
            activo: boolean;
        }>;
        links: Array<{ url: string; label: string; active: boolean }>;
        total: number;
    };
    filtros: {
        buscar?: string;
        estado?: string;
    };
}>();

const buscar = ref(props.filtros.buscar ?? '');

let timeout: ReturnType<typeof setTimeout>;
watch(buscar, (valor) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/clientes', { buscar: valor }, {
            preserveState: true,
            replace: true,
        });
    }, 400);
});

async function eliminar(id: number, nombre: string) {
    const result = await swal.fire({
        title: '¿Eliminar cliente?',
        text: `Se eliminará a "${nombre}" del sistema.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(`/clientes/${id}`, {
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
    <Head title="Clientes" />

    <div class="p-6 space-y-6">

        <!-- Encabezado -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Clientes</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ clientes.total }} clientes registrados
                </p>
            </div>
            <Link href="/clientes/create">
                <Button class="gap-2">
                    <Plus class="w-4 h-4" />
                    Nuevo cliente
                </Button>
            </Link>
        </div>

        <!-- Buscador -->
        <div class="relative max-w-sm">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <Input
                v-model="buscar"
                placeholder="Buscar por nombre, NIT o teléfono..."
                class="pl-9"
            />
        </div>

        <!-- Tabla -->
        <div class="border rounded-lg overflow-hidden">
            <Table>
                <TableHeader>
                    <TableRow class="bg-gray-50">
                        <TableHead>Cliente</TableHead>
                        <TableHead>NIT</TableHead>
                        <TableHead>Contacto</TableHead>
                        <TableHead>Departamento</TableHead>
                        <TableHead>Tipo</TableHead>
                        <TableHead>Estado</TableHead>
                        <TableHead class="text-right">Acciones</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="cliente in clientes.data"
                        :key="cliente.id"
                        class="hover:bg-gray-50"
                    >
                        <TableCell class="font-medium">
                            {{ cliente.nombre }}
                        </TableCell>
                        <TableCell class="text-gray-500 font-mono text-sm">
                            {{ cliente.nit ?? 'CF' }}
                        </TableCell>
                        <TableCell>
                            <div class="space-y-0.5">
                                <div v-if="cliente.telefono"
                                     class="flex items-center gap-1 text-sm text-gray-600">
                                    <Phone class="w-3 h-3" />
                                    {{ cliente.telefono }}
                                </div>
                                <div v-if="cliente.email"
                                     class="flex items-center gap-1 text-sm text-gray-600">
                                    <Mail class="w-3 h-3" />
                                    {{ cliente.email }}
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="text-gray-500 text-sm">
                            {{ cliente.departamento ?? '—' }}
                        </TableCell>
                        <TableCell>
                            <Badge variant="outline" class="text-xs">
                                {{ cliente.tipo === 'NATURAL' ? 'Personal' : 'Empresa' }}
                            </Badge>
                        </TableCell>
                        <TableCell>
                            <Badge
                                :class="cliente.activo
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'"
                                class="text-xs"
                            >
                                {{ cliente.activo ? 'Activo' : 'Inactivo' }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="`/clientes/${cliente.id}/edit`">
                                    <Button variant="ghost" size="sm" class="gap-1">
                                        <Pencil class="w-3.5 h-3.5" />
                                        Editar
                                    </Button>
                                </Link>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="gap-1 text-red-600 hover:text-red-700 hover:bg-red-50"
                                    @click="eliminar(cliente.id, cliente.nombre)"
                                >
                                    <Trash2 class="w-3.5 h-3.5" />
                                    Eliminar
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <!-- Sin resultados -->
                    <TableRow v-if="clientes.data.length === 0">
                        <TableCell colspan="7" class="text-center py-12 text-gray-400">
                            No se encontraron clientes
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Paginación -->
        <div class="flex items-center gap-1">
            <template v-for="link in clientes.links" :key="link.label">
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