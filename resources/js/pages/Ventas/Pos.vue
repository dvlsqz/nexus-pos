<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Select, SelectContent, SelectItem,
    SelectTrigger, SelectValue,
} from '@/components/ui/select';
import { Search, Plus, Minus, Trash2, ShoppingCart, CreditCard, Banknote, Receipt } from 'lucide-vue-next';
import swal from '@/lib/swal';

defineOptions({ layout: AppLayout });

const props = defineProps<{
    clientes: Array<{ id: number; nombre: string; nit: string }>;
    productos: Array<{
        id: number;
        nombre: string;
        codigo: string;
        precio_venta: number;
        stock_actual: number;
        aplica_iva: boolean;
        porcentaje_iva: number;
        unidad_medida: string;
        categoria_id: number;
    }>;
}>();

// ── Estado del POS ──────────────────────────────────────────
const buscarProducto = ref('');
const clienteId      = ref('');
const tipoPago       = ref('CONTADO');
const metodoPago     = ref('EFECTIVO');
const tipoDocumento  = ref('COMPROBANTE');
const montoPagado    = ref(0);
const observaciones  = ref('');
const procesando     = ref(false);
const ventaExitosa   = ref<any>(null);

interface ItemCarrito {
    producto_id: number;
    nombre: string;
    codigo: string;
    cantidad: number;
    precio_unitario: number;
    descuento_porcentaje: number;
    aplica_iva: boolean;
    porcentaje_iva: number;
    unidad_medida: string;
}

const carrito = ref<ItemCarrito[]>([]);

// ── Filtro de productos ─────────────────────────────────────
const productosFiltrados = computed(() => {
    const q = buscarProducto.value.toLowerCase();
    if (!q) return props.productos.slice(0, 20);
    return props.productos.filter(p =>
        p.nombre.toLowerCase().includes(q) ||
        p.codigo.toLowerCase().includes(q)
    ).slice(0, 20);
});

// ── Carrito ─────────────────────────────────────────────────
function agregarProducto(producto: typeof props.productos[0]) {
    if (producto.stock_actual <= 0) {
        swal.fire({
            title: 'Sin stock',
            text: `${producto.nombre} no tiene stock disponible.`,
            icon: 'warning',
            confirmButtonColor: '#3b82f6',
        });
        return;
    }

    const existente = carrito.value.find(i => i.producto_id === producto.id);
    if (existente) {
        existente.cantidad += 1;
    } else {
        carrito.value.push({
            producto_id:          producto.id,
            nombre:               producto.nombre,
            codigo:               producto.codigo,
            cantidad:             1,
            precio_unitario:      producto.precio_venta,
            descuento_porcentaje: 0,
            aplica_iva:           producto.aplica_iva,
            porcentaje_iva:       producto.porcentaje_iva,
            unidad_medida:        producto.unidad_medida,
        });
    }
}

function cambiarCantidad(index: number, delta: number) {
    const item = carrito.value[index];
    const nueva = item.cantidad + delta;
    if (nueva <= 0) {
        carrito.value.splice(index, 1);
    } else {
        item.cantidad = nueva;
    }
}

function eliminarItem(index: number) {
    carrito.value.splice(index, 1);
}

function limpiarCarrito() {
    carrito.value = [];
    clienteId.value = '';
    metodoPago.value = 'EFECTIVO';
    tipoDocumento.value = 'COMPROBANTE';
    montoPagado.value = 0;
    observaciones.value = '';
    ventaExitosa.value = null;
}

// ── Cálculos ─────────────────────────────────────────────────
function calcularLinea(item: ItemCarrito) {
    const precioSinIva = item.aplica_iva
        ? item.precio_unitario / (1 + item.porcentaje_iva / 100)
        : item.precio_unitario;

    const subtotal      = precioSinIva * item.cantidad;
    const descuento     = subtotal * item.descuento_porcentaje / 100;
    const base          = subtotal - descuento;
    const iva           = item.aplica_iva ? base * item.porcentaje_iva / 100 : 0;
    const total         = base + iva;

    return { precioSinIva, subtotal, descuento, base, iva, total };
}

const totales = computed(() => {
    let baseImponible = 0;
    let totalIva      = 0;
    let totalExento   = 0;

    for (const item of carrito.value) {
        const calc = calcularLinea(item);
        if (item.aplica_iva) {
            baseImponible += calc.base;
            totalIva      += calc.iva;
        } else {
            totalExento   += calc.base;
        }
    }

    const total = baseImponible + totalIva + totalExento;
    const cambio = Math.max(0, montoPagado.value - total);

    return { baseImponible, totalIva, totalExento, total, cambio };
});

function formatGTQ(monto: number): string {
    return 'Q ' + monto.toLocaleString('es-GT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

// ── Procesar venta ───────────────────────────────────────────
async function procesarVenta() {
    if (!clienteId.value) {
        swal.fire({ title: 'Selecciona un cliente', icon: 'warning', confirmButtonColor: '#3b82f6' });
        return;
    }
    if (carrito.value.length === 0) {
        swal.fire({ title: 'El carrito está vacío', icon: 'warning', confirmButtonColor: '#3b82f6' });
        return;
    }
    if (metodoPago.value === 'EFECTIVO' && montoPagado.value < totales.value.total) {
        swal.fire({
            title: 'Monto insuficiente',
            text: `El total es ${formatGTQ(totales.value.total)}`,
            icon: 'warning',
            confirmButtonColor: '#3b82f6',
        });
        return;
    }

    procesando.value = true;

    try {
        const response = await fetch('/ventas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
            },
            body: JSON.stringify({
                cliente_id:     clienteId.value,
                tipo_pago:      tipoPago.value,
                metodo_pago:    metodoPago.value,
                tipo_documento: tipoDocumento.value,
                monto_pagado:   montoPagado.value,
                observaciones:  observaciones.value,
                items:          carrito.value.map(i => ({
                    producto_id:          i.producto_id,
                    cantidad:             i.cantidad,
                    precio_unitario:      i.precio_unitario,
                    descuento_porcentaje: i.descuento_porcentaje,
                })),
            }),
        });

        const data = await response.json();

        if (data.success) {
            ventaExitosa.value = data.venta;
            await swal.fire({
                title: '¡Venta completada!',
                html: `
                    <p class="text-lg font-bold">${data.numero}</p>
                    <p class="text-gray-600">Total: <strong>${formatGTQ(data.venta.total)}</strong></p>
                    <p class="text-gray-600">Cambio: <strong>${formatGTQ(totales.value.cambio)}</strong></p>
                `,
                icon: 'success',
                confirmButtonText: 'Nueva venta',
                confirmButtonColor: '#2563eb',
            });
            limpiarCarrito();
        } else {
            swal.fire({
                title: 'Error',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#3b82f6',
            });
        }
    } catch (e) {
        swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo procesar la venta.',
            icon: 'error',
            confirmButtonColor: '#3b82f6',
        });
    } finally {
        procesando.value = false;
    }
}
</script>

<template>
    <Head title="Punto de Venta" />

    <div class="flex h-[calc(100vh-4rem)] overflow-hidden">

        <!-- Panel izquierdo — Productos -->
        <div class="flex-1 flex flex-col border-r overflow-hidden">

            <!-- Buscador -->
            <div class="p-4 border-b bg-white">
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <Input
                        v-model="buscarProducto"
                        placeholder="Buscar producto por nombre o código..."
                        class="pl-9"
                        autofocus
                    />
                </div>
            </div>

            <!-- Grid de productos -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <button
                        v-for="producto in productosFiltrados"
                        :key="producto.id"
                        @click="agregarProducto(producto)"
                        class="text-left p-3 border rounded-lg hover:border-blue-400
                               hover:bg-blue-50 transition group relative"
                        :class="producto.stock_actual <= 0
                            ? 'opacity-50 cursor-not-allowed'
                            : 'cursor-pointer'"
                    >
                        <div class="flex items-start justify-between gap-1">
                            <p class="text-sm font-medium text-gray-800 leading-tight line-clamp-2">
                                {{ producto.nombre }}
                            </p>
                            <Plus class="w-4 h-4 text-blue-500 opacity-0 group-hover:opacity-100
                                        flex-shrink-0 mt-0.5 transition" />
                        </div>
                        <p class="text-xs text-gray-400 mt-1 font-mono">{{ producto.codigo }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-blue-700 font-bold text-sm">
                                {{ formatGTQ(producto.precio_venta) }}
                            </span>
                            <Badge
                                class="text-xs"
                                :class="producto.stock_actual <= 0
                                    ? 'bg-red-100 text-red-600'
                                    : 'bg-gray-100 text-gray-600'"
                            >
                                {{ producto.stock_actual }} {{ producto.unidad_medida }}
                            </Badge>
                        </div>
                    </button>
                </div>

                <p v-if="productosFiltrados.length === 0"
                   class="text-center text-gray-400 py-12">
                    No se encontraron productos
                </p>
            </div>
        </div>

        <!-- Panel derecho — Carrito y cobro -->
        <div class="w-96 flex flex-col bg-white overflow-hidden">

            <!-- Cliente y documento -->
            <div class="p-4 border-b space-y-3">
                <Select v-model="clienteId">
                    <SelectTrigger>
                        <SelectValue placeholder="Seleccionar cliente..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="cliente in clientes"
                            :key="cliente.id"
                            :value="String(cliente.id)"
                        >
                            {{ cliente.nombre }}
                            <span v-if="cliente.nit" class="text-gray-400 text-xs ml-1">
                                ({{ cliente.nit }})
                            </span>
                        </SelectItem>
                    </SelectContent>
                </Select>

                <div class="grid grid-cols-2 gap-2">
                    <Select v-model="tipoDocumento">
                        <SelectTrigger class="text-xs">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="COMPROBANTE">Comprobante</SelectItem>
                            <SelectItem value="FACTURA">Factura</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="metodoPago">
                        <SelectTrigger class="text-xs">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="EFECTIVO">Efectivo</SelectItem>
                            <SelectItem value="TARJETA">Tarjeta</SelectItem>
                            <SelectItem value="TRANSFERENCIA">Transferencia</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Items del carrito -->
            <div class="flex-1 overflow-y-auto">
                <div v-if="carrito.length === 0"
                     class="flex flex-col items-center justify-center h-full text-gray-400">
                    <ShoppingCart class="w-12 h-12 mb-2 opacity-30" />
                    <p class="text-sm">Agrega productos al carrito</p>
                </div>

                <div v-else class="divide-y">
                    <div
                        v-for="(item, index) in carrito"
                        :key="item.producto_id"
                        class="p-3"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">
                                    {{ item.nombre }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ formatGTQ(item.precio_unitario) }} / {{ item.unidad_medida }}
                                </p>
                            </div>
                            <button
                                @click="eliminarItem(index)"
                                class="text-red-400 hover:text-red-600 transition flex-shrink-0"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>

                        <div class="flex items-center justify-between mt-2">
                            <!-- Cantidad -->
                            <div class="flex items-center gap-1">
                                <button
                                    @click="cambiarCantidad(index, -1)"
                                    class="w-6 h-6 rounded border flex items-center justify-center
                                           hover:bg-gray-100 transition"
                                >
                                    <Minus class="w-3 h-3" />
                                </button>
                                <input
                                    v-model.number="item.cantidad"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    class="w-14 text-center text-sm border rounded px-1 py-0.5"
                                />
                                <button
                                    @click="cambiarCantidad(index, 1)"
                                    class="w-6 h-6 rounded border flex items-center justify-center
                                           hover:bg-gray-100 transition"
                                >
                                    <Plus class="w-3 h-3" />
                                </button>
                            </div>

                            <!-- Total línea -->
                            <span class="text-sm font-bold text-gray-800">
                                {{ formatGTQ(calcularLinea(item).total) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Totales y cobro -->
            <div class="border-t p-4 space-y-3 bg-gray-50">

                <!-- Desglose IVA -->
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Base imponible</span>
                        <span>{{ formatGTQ(totales.baseImponible) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>IVA (12%)</span>
                        <span>{{ formatGTQ(totales.totalIva) }}</span>
                    </div>
                    <div v-if="totales.totalExento > 0" class="flex justify-between text-gray-500">
                        <span>Exento</span>
                        <span>{{ formatGTQ(totales.totalExento) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg text-gray-900 pt-1 border-t">
                        <span>Total</span>
                        <span class="text-blue-700">{{ formatGTQ(totales.total) }}</span>
                    </div>
                </div>

                <!-- Monto pagado y cambio -->
                <div v-if="metodoPago === 'EFECTIVO'" class="space-y-2">
                    <div class="flex items-center gap-2">
                        <Banknote class="w-4 h-4 text-gray-400" />
                        <Input
                            v-model.number="montoPagado"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="Monto recibido"
                            class="text-right font-mono"
                        />
                    </div>
                    <div v-if="montoPagado > 0"
                         class="flex justify-between text-sm font-medium"
                         :class="totales.cambio >= 0 ? 'text-green-600' : 'text-red-500'"
                    >
                        <span>Cambio</span>
                        <span>{{ formatGTQ(totales.cambio) }}</span>
                    </div>
                </div>

                <!-- Botón cobrar -->
                <Button
                    @click="procesarVenta"
                    :disabled="procesando || carrito.length === 0"
                    class="w-full gap-2 h-12 text-base"
                >
                    <CreditCard class="w-5 h-5" />
                    {{ procesando ? 'Procesando...' : 'Cobrar ' + formatGTQ(totales.total) }}
                </Button>

                <!-- Limpiar -->
                <button
                    v-if="carrito.length > 0"
                    @click="limpiarCarrito"
                    class="w-full text-xs text-gray-400 hover:text-red-500 transition"
                >
                    Limpiar carrito
                </button>
            </div>
        </div>
    </div>
</template>