<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    ShoppingCart, TrendingUp, Package, Users,
    AlertTriangle, ArrowUpRight, ArrowDownRight,
    Truck, BarChart3,
} from 'lucide-vue-next';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    stats: {
        ventas_hoy:          { cantidad: number; monto: number };
        ventas_mes:          { cantidad: number; monto: number };
        ventas_mes_anterior: number;
        stock_bajo:          number;
        total_clientes:      number;
        total_productos:     number;
        total_proveedores:   number;
    };
    ultimas_ventas: Array<{
        id: number;
        numero: string;
        fecha: string;
        total: number;
        metodo_pago: string;
        cliente: { nombre: string };
        user: { name: string };
    }>;
    ventas_semana: Array<{
        fecha: string;
        cantidad: number;
        monto: number;
    }>;
    top_productos: Array<{
        nombre: string;
        total_cantidad: number;
        total_monto: number;
    }>;
    productos_stock_bajo: Array<{
        id: number;
        nombre: string;
        stock_actual: number;
        stock_minimo: number;
        unidad_medida: string;
        categoria: { nombre: string; color: string };
    }>;
}>();

function formatGTQ(monto: number): string {
    return 'Q ' + Number(monto).toLocaleString('es-GT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function formatFecha(fecha: string): string {
    return new Date(fecha).toLocaleDateString('es-GT', {
        day: '2-digit', month: '2-digit', year: 'numeric',
    });
}

// Calcular variación vs mes anterior
const variacionMes = props.stats.ventas_mes_anterior > 0
    ? ((props.stats.ventas_mes.monto - props.stats.ventas_mes_anterior)
       / props.stats.ventas_mes_anterior * 100).toFixed(1)
    : null;

const variacionPositiva = Number(variacionMes) >= 0;

// Máximo para la gráfica de barras
const maxMonto = Math.max(...props.ventas_semana.map(v => v.monto), 1);

const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

function nombreDia(fecha: string): string {
    return diasSemana[new Date(fecha).getDay()];
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="p-6 space-y-6">

        <!-- Encabezado -->
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ new Date().toLocaleDateString('es-GT', {
                    weekday: 'long', day: 'numeric',
                    month: 'long', year: 'numeric'
                }) }}
            </p>
        </div>

        <!-- Cards principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Ventas hoy -->
            <div class="bg-white border rounded-xl p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Ventas hoy</span>
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                        <ShoppingCart class="w-4 h-4 text-blue-600" />
                    </div>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ formatGTQ(stats.ventas_hoy.monto) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ stats.ventas_hoy.cantidad }} transacciones
                    </p>
                </div>
            </div>

            <!-- Ventas del mes -->
            <div class="bg-white border rounded-xl p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Ventas del mes</span>
                    <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                        <TrendingUp class="w-4 h-4 text-green-600" />
                    </div>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ formatGTQ(stats.ventas_mes.monto) }}
                    </p>
                    <div v-if="variacionMes !== null"
                         class="flex items-center gap-1 mt-0.5">
                        <component
                            :is="variacionPositiva ? ArrowUpRight : ArrowDownRight"
                            class="w-3.5 h-3.5"
                            :class="variacionPositiva ? 'text-green-500' : 'text-red-500'"
                        />
                        <span
                            class="text-xs font-medium"
                            :class="variacionPositiva ? 'text-green-600' : 'text-red-600'"
                        >
                            {{ variacionPositiva ? '+' : '' }}{{ variacionMes }}% vs mes anterior
                        </span>
                    </div>
                    <p v-else class="text-xs text-gray-400 mt-0.5">
                        {{ stats.ventas_mes.cantidad }} transacciones
                    </p>
                </div>
            </div>

            <!-- Stock bajo -->
            <div class="bg-white border rounded-xl p-5 space-y-3"
                 :class="stats.stock_bajo > 0 ? 'border-amber-200' : ''">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Stock bajo</span>
                    <div
                        class="w-9 h-9 rounded-lg flex items-center justify-center"
                        :class="stats.stock_bajo > 0 ? 'bg-amber-100' : 'bg-gray-100'"
                    >
                        <AlertTriangle
                            class="w-4 h-4"
                            :class="stats.stock_bajo > 0 ? 'text-amber-600' : 'text-gray-400'"
                        />
                    </div>
                </div>
                <div>
                    <p class="text-2xl font-bold"
                       :class="stats.stock_bajo > 0 ? 'text-amber-600' : 'text-gray-900'">
                        {{ stats.stock_bajo }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">productos bajo mínimo</p>
                </div>
            </div>

            <!-- Clientes -->
            <div class="bg-white border rounded-xl p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Clientes activos</span>
                    <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                        <Users class="w-4 h-4 text-purple-600" />
                    </div>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.total_clientes }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ stats.total_proveedores }} proveedores
                    </p>
                </div>
            </div>

        </div>

        <!-- Gráfica de ventas + Top productos -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Gráfica barras — últimos 7 días -->
            <div class="lg:col-span-2 bg-white border rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-700">
                        Ventas últimos 7 días
                    </h2>
                    <BarChart3 class="w-4 h-4 text-gray-400" />
                </div>

                <div v-if="ventas_semana.length > 0"
                     class="flex items-end gap-2 h-36">
                    <div
                        v-for="dia in ventas_semana"
                        :key="dia.fecha"
                        class="flex-1 flex flex-col items-center gap-1"
                    >
                        <span class="text-xs text-gray-500">
                            {{ formatGTQ(dia.monto) }}
                        </span>
                        <div
                            class="w-full bg-blue-500 rounded-t-md transition-all"
                            :style="{
                                height: Math.max((dia.monto / maxMonto) * 100, 4) + 'px'
                            }"
                        />
                        <span class="text-xs text-gray-400">
                            {{ nombreDia(dia.fecha) }}
                        </span>
                    </div>
                </div>

                <div v-else class="h-36 flex items-center justify-center text-gray-400 text-sm">
                    No hay ventas en los últimos 7 días
                </div>
            </div>

            <!-- Top productos del mes -->
            <div class="bg-white border rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">
                    Top productos del mes
                </h2>

                <div v-if="top_productos.length > 0" class="space-y-3">
                    <div
                        v-for="(producto, index) in top_productos"
                        :key="producto.nombre"
                        class="flex items-center gap-3"
                    >
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700
                                     text-xs flex items-center justify-center font-bold flex-shrink-0">
                            {{ index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                {{ producto.nombre }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ producto.total_cantidad }} unidades
                            </p>
                        </div>
                        <span class="text-sm font-bold text-gray-700 flex-shrink-0">
                            {{ formatGTQ(producto.total_monto) }}
                        </span>
                    </div>
                </div>

                <p v-else class="text-sm text-gray-400 text-center py-4">
                    Sin ventas este mes
                </p>
            </div>
        </div>

        <!-- Últimas ventas + Stock bajo -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            <!-- Últimas ventas -->
            <div class="bg-white border rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-700">Últimas ventas</h2>
                    <Link href="/ventas" class="text-xs text-blue-600 hover:underline">
                        Ver todas
                    </Link>
                </div>

                <div v-if="ultimas_ventas.length > 0" class="space-y-3">
                    <div
                        v-for="venta in ultimas_ventas"
                        :key="venta.id"
                        class="flex items-center justify-between py-2 border-b last:border-0"
                    >
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 font-mono">
                                {{ venta.numero }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">
                                {{ venta.cliente.nombre }} · {{ formatFecha(venta.fecha) }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0 ml-3">
                            <p class="text-sm font-bold text-gray-900">
                                {{ formatGTQ(venta.total) }}
                            </p>
                            <p class="text-xs text-gray-400">{{ venta.metodo_pago }}</p>
                        </div>
                    </div>
                </div>

                <p v-else class="text-sm text-gray-400 text-center py-4">
                    No hay ventas registradas
                </p>
            </div>

            <!-- Productos stock bajo -->
            <div class="bg-white border rounded-xl p-5"
                 :class="productos_stock_bajo.length > 0 ? 'border-amber-200' : ''">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-700">
                        Productos con stock bajo
                    </h2>
                    <Link href="/inventario" class="text-xs text-blue-600 hover:underline">
                        Ver inventario
                    </Link>
                </div>

                <div v-if="productos_stock_bajo.length > 0" class="space-y-3">
                    <div
                        v-for="producto in productos_stock_bajo"
                        :key="producto.id"
                        class="flex items-center justify-between py-2 border-b last:border-0"
                    >
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                {{ producto.nombre }}
                            </p>
                            <span
                                class="text-xs px-1.5 py-0.5 rounded-full"
                                :style="{
                                    backgroundColor: producto.categoria.color + '20',
                                    color: producto.categoria.color,
                                }"
                            >
                                {{ producto.categoria.nombre }}
                            </span>
                        </div>
                        <div class="text-right flex-shrink-0 ml-3">
                            <p class="text-sm font-bold text-amber-600">
                                {{ producto.stock_actual }} {{ producto.unidad_medida }}
                            </p>
                            <p class="text-xs text-gray-400">
                                Mín: {{ producto.stock_minimo }}
                            </p>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center py-6 text-center">
                    <Package class="w-8 h-8 text-green-400 mb-2" />
                    <p class="text-sm text-gray-400">
                        Todos los productos tienen stock suficiente
                    </p>
                </div>

            </div>
        </div>

    </div>
</template>