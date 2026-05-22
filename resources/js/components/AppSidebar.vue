<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    LayoutGrid,
    ShoppingCart,
    Package,
    Warehouse,
    Users,
    UserCircle,
    Truck,
    FileText,
    Settings,
    ClipboardList,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroup,
    SidebarGroupLabel,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const ventasNavItems: NavItem[] = [
    {
        title: 'Punto de Venta',
        href: '/ventas/pos',
        icon: ShoppingCart,
    },
    {
        title: 'Ventas',
        href: '/ventas',
        icon: ClipboardList,
    },
    {
        title: 'Cotizaciones',
        href: '/cotizaciones',
        icon: FileText,
    },
    {
        title: 'Clientes',
        href: '/clientes',
        icon: UserCircle,
    },
];

const inventarioNavItems: NavItem[] = [
    {
        title: 'Productos',
        href: '/productos',
        icon: Package,
    },
    {
        title: 'Inventario',
        href: '/inventario',
        icon: Warehouse,
    },
    {
        title: 'Compras',
        href: '/compras',
        icon: Truck,
    },
    {
        title: 'Proveedores',
        href: '/proveedores',
        icon: Users,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Usuarios',
        href: '/usuarios',
        icon: Users,
    },
    {
        title: 'Configuración',
        href: '/configuracion',
        icon: Settings,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">

        <!-- Logo -->
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>

            <!-- General -->
            <NavMain :items="mainNavItems" />

            <!-- Ventas -->
            <SidebarGroup>
                <SidebarGroupLabel>Ventas</SidebarGroupLabel>
                <NavMain :items="ventasNavItems" />
            </SidebarGroup>

            <!-- Inventario -->
            <SidebarGroup>
                <SidebarGroupLabel>Inventario</SidebarGroupLabel>
                <NavMain :items="inventarioNavItems" />
            </SidebarGroup>

            <!-- Administración -->
            <SidebarGroup>
                <SidebarGroupLabel>Administración</SidebarGroupLabel>
                <NavMain :items="adminNavItems" />
            </SidebarGroup>

        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>

    </Sidebar>
    <slot />
</template>