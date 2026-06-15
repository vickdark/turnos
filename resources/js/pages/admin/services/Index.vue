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
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 shadow-sm dark:bg-[#161615] dark:border-[#3E3E3A]">
                <h2 class="mb-6 text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Crear Nuevo Servicio</h2>
                <form @submit.prevent="submit" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="grid gap-2">
                        <Label for="name" class="dark:text-[#EDEDEC]">Nombre del Servicio</Label>
                        <Input id="name" v-model="form.name" placeholder="Ej: Consulta Médica" required class="dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="type" class="dark:text-[#EDEDEC]">Tipo de Gestión</Label>
                        <select 
                            id="type" 
                            v-model="form.type"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]"
                        >
                            <option value="scheduled" class="dark:bg-[#161615]">Reserva por Calendario</option>
                            <option value="sequential" class="dark:bg-[#161615]">Turno Secuencial (Sala de espera)</option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="duration" class="dark:text-[#EDEDEC]">Duración (minutos)</Label>
                        <Input id="duration" type="number" v-model="form.duration_minutes" required class="dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]" />
                    </div>

                    <div class="grid gap-2 lg:col-span-2">
                        <Label for="description" class="dark:text-[#EDEDEC]">Descripción</Label>
                        <Input id="description" v-model="form.description" placeholder="Breve descripción del servicio" class="dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]" />
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
                    class="relative rounded-xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:bg-[#161615] dark:border-[#3E3E3A]"
                >
                    <div class="mb-3 flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ service.name }}</h3>
                            <div class="mt-1 flex items-center gap-2 text-xs text-muted-foreground dark:text-[#A1A09A]">
                                <component :is="service.type === 'scheduled' ? CalendarIcon : ListOrderedIcon" class="h-3 w-3" />
                                {{ service.type === 'scheduled' ? 'Calendario' : 'Secuencial' }}
                            </div>
                        </div>
                        <button @click="deleteService(service.id)" class="text-red-500 hover:text-red-700 transition-colors">
                            <TrashIcon class="h-4 w-4" />
                        </button>
                    </div>
                    
                    <p class="mb-4 text-sm text-muted-foreground dark:text-[#A1A09A]">{{ service.description || 'Sin descripción' }}</p>
                    
                    <div class="flex items-center justify-between border-t pt-4 text-sm dark:border-[#3E3E3A]">
                        <span class="dark:text-[#EDEDEC]">{{ service.duration_minutes }} min</span>
                        <span :class="service.is_active ? 'text-green-500' : 'text-red-500'" class="font-medium">
                            {{ service.is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
