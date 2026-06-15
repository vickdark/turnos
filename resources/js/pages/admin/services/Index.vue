<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PlusIcon, TrashIcon, CalendarIcon, ListOrderedIcon } from 'lucide-vue-next';

interface Service {
    id: number;
    name: string;
    description: string;
    type: 'sequential' | 'scheduled';
    duration_minutes: number;
    capacity_per_slot: number;
    is_active: boolean;
}

defineProps<{
    services: Service[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Panel de Control', href: '/dashboard' },
    { title: 'Servicios', href: '/admin/services' },
];

const form = useForm({
    name: '',
    description: '',
    type: 'scheduled',
    duration_minutes: 30,
    capacity_per_slot: 1,
});

const submit = () => {
    form.post(route('admin.services.store'), {
        onSuccess: () => form.reset(),
    });
};

const deleteService = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este servicio?')) {
        useForm({}).delete(route('admin.services.destroy', id));
    }
};
</script>

<template>
    <Head title="Gestión de Servicios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 p-4 md:p-8">
            <!-- Formulario de Creación -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:bg-[#161615]">
                <h2 class="mb-6 text-lg font-semibold">Crear Nuevo Servicio</h2>
                <form @submit.prevent="submit" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="grid gap-2">
                        <Label for="name">Nombre del Servicio</Label>
                        <Input id="name" v-model="form.name" placeholder="Ej: Consulta Médica" required />
                    </div>

                    <div class="grid gap-2">
                        <Label for="type">Tipo de Gestión</Label>
                        <select 
                            id="type" 
                            v-model="form.type"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="scheduled">Reserva por Calendario</option>
                            <option value="sequential">Turno Secuencial (Sala de espera)</option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="duration">Duración (minutos)</Label>
                        <Input id="duration" type="number" v-model="form.duration_minutes" required />
                    </div>

                    <div class="grid gap-2 lg:col-span-2">
                        <Label for="description">Descripción</Label>
                        <Input id="description" v-model="form.description" placeholder="Breve descripción del servicio" />
                    </div>

                    <div class="flex items-end">
                        <Button type="submit" class="w-full" :disabled="form.processing">
                            <PlusIcon class="mr-2 h-4 w-4" />
                            Crear Servicio
                        </Button>
                    </div>
                </form>
            </div>

            <!-- Lista de Servicios -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div 
                    v-for="service in services" 
                    :key="service.id"
                    class="relative rounded-xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:bg-[#161615]"
                >
                    <div class="mb-3 flex items-start justify-between">
                        <div>
                            <h3 class="font-bold">{{ service.name }}</h3>
                            <div class="mt-1 flex items-center gap-2 text-xs text-muted-foreground">
                                <component :is="service.type === 'scheduled' ? CalendarIcon : ListOrderedIcon" class="h-3 w-3" />
                                {{ service.type === 'scheduled' ? 'Calendario' : 'Secuencial' }}
                            </div>
                        </div>
                        <button @click="deleteService(service.id)" class="text-red-500 hover:text-red-700">
                            <TrashIcon class="h-4 w-4" />
                        </button>
                    </div>
                    
                    <p class="mb-4 text-sm text-muted-foreground">{{ service.description || 'Sin descripción' }}</p>
                    
                    <div class="flex items-center justify-between border-t pt-4 text-sm">
                        <span>{{ service.duration_minutes }} min</span>
                        <span :class="service.is_active ? 'text-green-500' : 'text-red-500'">
                            {{ service.is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
