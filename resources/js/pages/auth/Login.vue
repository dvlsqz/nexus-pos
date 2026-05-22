<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import AuthSplitLayout from '@/layouts/auth/AuthSplitLayout.vue';

defineOptions({
    layout: AuthSplitLayout,
    layoutProps: {
        title: 'Iniciar sesión',
        description: 'Ingresa tus credenciales para continuar',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Iniciar sesión" />

    <!-- Status mensaje -->
    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <!-- Formulario -->
    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <!-- Email -->
        <div class="grid gap-2">
            <Label for="email">Correo electrónico</Label>
            <Input
                id="email"
                type="email"
                name="email"
                required
                autofocus
                :tabindex="1"
                autocomplete="email"
                placeholder="correo@empresa.com"
            />
            <InputError :message="errors.email" />
        </div>

        <!-- Contraseña -->
        <div class="grid gap-2">
            <div class="flex items-center justify-between">
                <Label for="password">Contraseña</Label>
                <TextLink
                    v-if="canResetPassword"
                    :href="request()"
                    class="text-sm"
                    :tabindex="5"
                >
                    ¿Olvidaste tu contraseña?
                </TextLink>
            </div>
            <PasswordInput
                id="password"
                name="password"
                required
                :tabindex="2"
                autocomplete="current-password"
                placeholder="••••••••"
            />
            <InputError :message="errors.password" />
        </div>

        <!-- Recordarme -->
        <Label for="remember" class="flex items-center gap-3 cursor-pointer">
            <Checkbox id="remember" name="remember" :tabindex="3" />
            <span class="text-sm">Recordar sesión</span>
        </Label>

        <!-- Botón -->
        <Button
            type="submit"
            class="w-full"
            :tabindex="4"
            :disabled="processing"
        >
            <Spinner v-if="processing" />
            {{ processing ? 'Iniciando sesión...' : 'Iniciar sesión' }}
        </Button>

    </Form>
</template>