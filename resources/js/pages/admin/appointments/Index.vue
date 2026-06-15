<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { 
    CalendarIcon, 
    ClockIcon, 
    UserIcon, 
    PhoneIcon, 
    TrashIcon, 
    CheckCircle2Icon, 
    XCircleIcon, 
    Clock4Icon,
    ListOrderedIcon
} from 'lucide-vue-next';

interface Appointment {
    id: number;
    sequence_number: number | null;
    start_time: string | null;
    status: 'pending' | 'confirmed' | 'cancelled' | 'completed';
    created_at: string;
    service: {
        name: string;
        type: 'sequential' | 'scheduled';
    };
    client: {
        first_name: string;
        last_name: string;
        phone: string;
    };
}

defineProps<{
    appointments: Appointment[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Panel de Control', href: '/dashboard' },
    { title: 'Turnos y Reservas', href: '/admin/appointments' },
];

const updateStatus = (id: number, status: string) => {
    useForm({ status }).patch(route('admin.appointments.update-status', id));
};

const deleteAppointment = (id: number) => {
    if (confirm('¿Estás seguro de eliminar este registro?')) {
        useForm({}).delete(route('admin.appointments.destroy', id));
    }
};

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const formatTime = (dateStr: string) => {
    return new Date(dateStr).toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusClass = (status: string) => {
    switch (status) {
        case 'confirmed': return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
        case 'cancelled': return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
        case 'completed': return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
        default: return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
    }
};
</script>

<template>
    <Head title="Gestión de Turnos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 p-4 md:p-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold dark:text-[#EDEDEC]">Turnos y Reservas</h1>
            </div>

            <div class="grid gap-4">
                <div v-if="appointments.length === 0" class="text-center py-20 bg-white dark:bg-[#161615] rounded-xl border border-sidebar-border/70 dark:border-[#3E3E3A]">
                    <p class="text-muted-foreground dark:text-[#A1A09A]">No hay turnos registrados aún.</p>
                </div>

                <div 
                    v-for="appointment in appointments" 
                    :key="appointment.id"
                    class="rounded-xl border border-sidebar-border/70 bg-white p-5 shadow-sm dark:bg-[#161615] dark:border-[#3E3E3A] flex flex-col md:flex-row md:items-center justify-between gap-4"
                >
                    <div class="flex items-start gap-4">
                        <div class="hidden sm:flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                            <component :is="appointment.service.type === 'sequential' ? ListOrderedIcon : CalendarIcon" class="h-6 w-6" />
                        </div>
                        
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold dark:text-[#EDEDEC]">{{ appointment.client.first_name }} {{ appointment.client.last_name }}</h3>
                                <span :class="['px-2 py-0.5 rounded-full text-[10px] font-bold uppercase', getStatusClass(appointment.status)]">
                                    {{ appointment.status === 'confirmed' ? 'Confirmado' : 
                                       appointment.status === 'cancelled' ? 'Cancelado' : 
                                       appointment.status === 'completed' ? 'Completado' : 'Pendiente' }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted-foreground dark:text-[#A1A09A]">
                                <span class="flex items-center gap-1"><PhoneIcon class="h-3 w-3" /> {{ appointment.client.phone }}</span>
                                <span class="flex items-center gap-1 font-medium text-blue-600 dark:text-blue-400">
                                    {{ appointment.service.name }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-muted-foreground dark:text-[#A1A09A]">
                                <span v-if="appointment.service.type === 'sequential'" class="flex items-center gap-1">
                                    <ListOrderedIcon class="h-3 w-3" /> Turno #{{ appointment.sequence_number }}
                                </span>
                                <span v-else class="flex items-center gap-1">
                                    <CalendarIcon class="h-3 w-3" /> {{ formatDate(appointment.start_time!) }} - {{ formatTime(appointment.start_time!) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <Clock4Icon class="h-3 w-3" /> Solicitado: {{ formatDate(appointment.created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end md:self-center">
                        <select 
                            @change="(e) => updateStatus(appointment.id, (e.target as HTMLSelectElement).value)"
                            :value="appointment.status"
                            class="h-8 rounded-md border border-input bg-transparent px-2 text-xs shadow-sm focus-visible:outline-none dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]"
                        >
                            <option value="confirmed">Confirmado</option>
                            <option value="completed">Completado</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                        <Button variant="ghost" size="icon" class="h-8 w-8 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30" @click="deleteAppointment(appointment.id)">
                            <TrashIcon class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
