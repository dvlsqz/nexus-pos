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
import { Plus, Search, Pencil, Trash2, ShieldCheck } from 'lucide-vue-next';
import swal from '@/lib/swal';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    usuarios: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            activo: boolean;
            ultimo_login: string | null;
            roles: Array<{ name: string }>;
        }>;
        links: Array<{ url: string; label: string; active: boolean }>;
        total: number;
    };
    roles: Array<{ id: number; name: string }>;
    filtros: { buscar?: string; rol?: string };
}>();

const buscar = ref(props.filtros.buscar ?? '');
const rol    = ref(props.filtros.rol ?? 'all');

const coloresRol: Record<string, string> = {
    'Administrador': 'bg-purple-100 text-purple-700',
    'Gerente':       'bg-blue-100 text-blue-700',
    'Vendedor':      'bg-green-100 text-green-700',
    'Bodeguero':     'bg-amber-100 text-amber-700',
};

let timeout: ReturnType<typeof setTimeout>;
watch(buscar, (valor) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/usuarios', {
            buscar: valor,
            rol: rol.value === 'all' ? '' : rol.value,
        }, { preserveState: true, replace: true });
    }, 400);
});

watch(rol, (valor) => {
    router.get('/usuarios', {
        buscar: buscar.value,
        rol: valor === 'all' ? '' : valor,
    }, { preserveState: true, replace: true });
});

function formatFecha(fecha: string | null): string {
    if (!fecha) return 'Nunca';
    return new Date(fecha).toLocaleDateString('es-GT', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}

async function eliminar(id: number, nombre: string) {
    const result = await swal.fire({
        title: '¿Eliminar usuario?',
        text: `Se eliminará a "${nombre}" del sistema.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(`/usuarios/${id}`, {
            onSuccess: () => {
                swal.fire({
                    title: '¡Eliminado!',
                    text: `${nombre} fue eliminado correctamente.`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
            onError: () => {
                swal.fire({
                    title: 'Error',
                    text: 'No se pudo eliminar el usuario.',
                    icon: 'error',
                    confirmButtonColor: '#3b82f6',
                });
            },
        });
    }
}
</script>

<template>
    <Head title="Usuarios" />

    <div class="p-6 space-y-6">

        <!-- Encabezado -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Usuarios</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ usuarios.total }} usuarios registrados
                </p>
            </div>
            <Link href="/usuarios/create">
                <Button class="gap-2">
                    <Plus class="w-4 h-4" />
                    Nuevo usuario
                </Button>
            </Link>
        </div>

        <!-- Filtros -->
        <div class="flex gap-3 flex-wrap">
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <Input
                    v-model="buscar"
                    placeholder="Buscar por nombre o correo..."
                    class="pl-9"
                />
            </div>
            <Select v-model="rol">
                <SelectTrigger class="w-44">
                    <SelectValue placeholder="Todos los roles" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">Todos los roles</SelectItem>
                    <SelectItem
                        v-for="r in roles"
                        :key="r.id"
                        :value="r.name"
                    >
                        {{ r.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>

        <!-- Tabla -->
        <div class="border rounded-lg overflow-hidden">
            <Table>
                <TableHeader>
                    <TableRow class="bg-gray-50">
                        <TableHead>Usuario</TableHead>
                        <TableHead>Correo</TableHead>
                        <TableHead>Rol</TableHead>
                        <TableHead>Último acceso</TableHead>
                        <TableHead>Estado</TableHead>
                        <TableHead class="text-right">Acciones</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="usuario in usuarios.data"
                        :key="usuario.id"
                        class="hover:bg-gray-50"
                    >
                        <TableCell>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center
                                            justify-center text-blue-700 font-semibold text-sm">
                                    {{ usuario.name.charAt(0).toUpperCase() }}
                                </div>
                                <span class="font-medium">{{ usuario.name }}</span>
                            </div>
                        </TableCell>
                        <TableCell class="text-gray-500 text-sm">
                            {{ usuario.email }}
                        </TableCell>
                        <TableCell>
                            <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="r in usuario.roles"
                                    :key="r.name"
                                    class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                                    :class="coloresRol[r.name] ?? 'bg-gray-100 text-gray-600'"
                                >
                                    <ShieldCheck class="w-3 h-3" />
                                    {{ r.name }}
                                </span>
                            </div>
                        </TableCell>
                        <TableCell class="text-sm text-gray-500">
                            {{ formatFecha(usuario.ultimo_login) }}
                        </TableCell>
                        <TableCell>
                            <Badge
                                :class="usuario.activo
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'"
                                class="text-xs"
                            >
                                {{ usuario.activo ? 'Activo' : 'Inactivo' }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="`/usuarios/${usuario.id}/edit`">
                                    <Button variant="ghost" size="sm" class="gap-1">
                                        <Pencil class="w-3.5 h-3.5" />
                                        Editar
                                    </Button>
                                </Link>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="gap-1 text-red-600 hover:text-red-700 hover:bg-red-50"
                                    @click="eliminar(usuario.id, usuario.name)"
                                >
                                    <Trash2 class="w-3.5 h-3.5" />
                                    Eliminar
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="usuarios.data.length === 0">
                        <TableCell colspan="6" class="text-center py-12 text-gray-400">
                            No se encontraron usuarios
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Paginación -->
        <div class="flex items-center gap-1">
            <template v-for="link in usuarios.links" :key="link.label">
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