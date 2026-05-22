<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="min-h-screen flex">

        <!-- Lado izquierdo — imagen + branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <!-- Imagen de fondo -->
            <img
                src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&q=80"
                alt="Nexus POS"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <!-- Overlay -->
            <div class="absolute inset-0 bg-blue-950/75" />

            <!-- Contenido -->
            <div class="relative z-10 flex flex-col justify-between p-12 w-full">
                <Link :href="home()" class="flex items-center gap-2">
                    <span class="text-3xl font-bold text-white tracking-tight">
                        Nexus<span class="text-blue-300">POS</span>
                    </span>
                </Link>

                <div class="space-y-3">
                    <p class="text-white/90 text-xl font-medium leading-relaxed">
                        "Gestiona tus ventas, inventario y clientes desde un solo lugar."
                    </p>
                    <p class="text-blue-200 text-sm">
                        Sistema de Punto de Venta · Guatemala
                    </p>
                </div>
            </div>
        </div>

        <!-- Lado derecho — formulario -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-background">
            <div class="w-full max-w-sm space-y-6">

                <!-- Logo solo en móvil -->
                <div class="lg:hidden text-center">
                    <Link :href="home()">
                        <span class="text-3xl font-bold tracking-tight">
                            Nexus<span class="text-blue-600">POS</span>
                        </span>
                    </Link>
                </div>

                <!-- Título y descripción -->
                <div class="flex flex-col space-y-1">
                    <h1 v-if="title" class="text-2xl font-semibold tracking-tight">
                        {{ title }}
                    </h1>
                    <p v-if="description" class="text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>

                <!-- Contenido de la página (formulario) -->
                <slot />

                <!-- Footer -->
                <p class="text-center text-xs text-muted-foreground pt-4">
                    Nexus POS © {{ new Date().getFullYear() }} · Guatemala
                </p>

            </div>
        </div>

    </div>
</template>