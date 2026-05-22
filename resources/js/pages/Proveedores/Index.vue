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
import { Plus, Search, Pencil, Trash2, Phone, Mail, Globe } from 'lucide-vue-next';
import swal from '@/lib/swal';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    proveedores: {
        data: Array<{
            id: number;
            nombre: string;
            nombre_comercial: string;
            nit: string;
            contacto_nombre: string;
            telefono: string;
            email: string;
            website: string;
            departamento: string;
            moneda: string;
            activo: boolean;
        }>;
        links: Array<{ url: string; label: string; active: boolean }>;
        total: number;
    };
    filtros: { buscar?: string };
}>();

const buscar = ref(props.filtros.buscar ?? '');

let timeout: ReturnType<typeof setTimeout>;
watch(buscar, (valor) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/proveedores', { buscar: valor }, {
            preserveState: true,
            replace: true,
        });
    }, 400);
});

async function eliminar(id: number, nombre: string) {
    const result = await swal.fire({
        title: '¿Eliminar proveedor?',
        text: `Se eliminará "${nombre}" del sistema.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(`/proveedores/${id}`, {
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
    <Head title="Proveedores" />

    <div class="p-6 space-y-6">

        <!-- Encabezado -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Proveedores</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ proveedores.total }} proveedores registrados
                </p>
            </div>
            <Link href="/proveedores/create">
                <Button class="gap-2">
                    <Plus class="w-4 h-4" />
                    Nuevo proveedor
                </Button>
            </Link>
        </div>

        <!-- Buscador -->
        <div class="relative max-w-sm">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <Input
                v-model="buscar"
                placeholder="Buscar por nombre, NIT o contacto..."
                class="pl-9"
            />
        </div>

        <!-- Tabla -->
        <div class="border rounded-lg overflow-hidden">
            <Table>
                <TableHeader>
                    <TableRow class="bg-gray-50">
                        <TableHead>Proveedor</TableHead>
                        <TableHead>NIT</TableHead>
                        <TableHead>Contacto</TableHead>
                        <TableHead>Ubicación</TableHead>
                        <TableHead>Moneda</TableHead>
                        <TableHead>Estado</TableHead>
                        <TableHead class="text-right">Acciones</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="proveedor in proveedores.data"
                        :key="proveedor.id"
                        class="hover:bg-gray-50"
                    >
                        <TableCell>
                            <div>
                                <p class="font-medium">{{ proveedor.nombre }}</p>
                                <p v-if="proveedor.nombre_comercial"
                                   class="text-xs text-gray-400">
                                    {{ proveedor.nombre_comercial }}
                                </p>
                                <p v-if="proveedor.contacto_nombre"
                                   class="text-xs text-gray-500 mt-0.5">
                                    Contacto: {{ proveedor.contacto_nombre }}
                                </p>
                            </div>
                        </TableCell>
                        <TableCell class="font-mono text-sm text-gray-500">
                            {{ proveedor.nit ?? '—' }}
                        </TableCell>
                        <TableCell>
                            <div class="space-y-0.5">
                                <div v-if="proveedor.telefono"
                                     class="flex items-center gap-1 text-sm text-gray-600">
                                    <Phone class="w-3 h-3" />
                                    {{ proveedor.telefono }}
                                </div>
                                <div v-if="proveedor.email"
                                     class="flex items-center gap-1 text-sm text-gray-600">
                                    <Mail class="w-3 h-3" />
                                    {{ proveedor.email }}
                                </div>
                                <div v-if="proveedor.website"
                                     class="flex items-center gap-1 text-sm text-gray-600">
                                    <Globe class="w-3 h-3" />
                                    {{ proveedor.website }}
                                </div>
                            </div>
                        </TableCell>
                        <TableCell class="text-sm text-gray-500">
                            {{ proveedor.departamento ?? '—' }}
                        </TableCell>
                        <TableCell>
                            <Badge
                                :class="proveedor.moneda === 'USD'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-blue-100 text-blue-700'"
                                class="text-xs font-mono"
                            >
                                {{ proveedor.moneda }}
                            </Badge>
                        </TableCell>
                        <TableCell>
                            <Badge
                                :class="proveedor.activo
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'"
                                class="text-xs"
                            >
                                {{ proveedor.activo ? 'Activo' : 'Inactivo' }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="`/proveedores/${proveedor.id}/edit`">
                                    <Button variant="ghost" size="sm" class="gap-1">
                                        <Pencil class="w-3.5 h-3.5" />
                                        Editar
                                    </Button>
                                </Link>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="gap-1 text-red-600 hover:text-red-700 hover:bg-red-50"
                                    @click="eliminar(proveedor.id, proveedor.nombre)"
                                >
                                    <Trash2 class="w-3.5 h-3.5" />
                                    Eliminar
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="proveedores.data.length === 0">
                        <TableCell colspan="7" class="text-center py-12 text-gray-400">
                            No se encontraron proveedores
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Paginación -->
        <div class="flex items-center gap-1">
            <template v-for="link in proveedores.links" :key="link.label">
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