<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2Icon, CalendarIcon, ClockIcon, UserIcon, PhoneIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

interface Appointment {
    id: number;
    sequence_number: number | null;
    start_time: string | null;
    status: string;
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
    appointment: Appointment;
}>();

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatTime = (dateStr: string) => {
    return new Date(dateStr).toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Turno Confirmado" />

    <div class="min-h-screen bg-[#FDFDFC] p-4 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC] flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="rounded-2xl border border-sidebar-border/70 bg-white p-8 shadow-lg dark:bg-[#161615] dark:border-[#3E3E3A] text-center space-y-6">
                <div class="flex justify-center">
                    <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/30">
                        <CheckCircle2Icon class="h-12 w-12 text-green-600 dark:text-green-400" />
                    </div>
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl font-bold">¡Turno Confirmado!</h1>
                    <p class="text-muted-foreground dark:text-[#A1A09A]">Tu reserva se ha realizado con éxito.</p>
                </div>

                <!-- Detalles del Turno Secuencial -->
                <div v-if="appointment.service.type === 'sequential'" class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 space-y-2">
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider">Tu número de turno</p>
                    <p class="text-6xl font-black text-blue-700 dark:text-blue-300">{{ appointment.sequence_number }}</p>
                    <p class="text-sm text-blue-600/80 dark:text-blue-400/80">Por favor, espera tu llamado en la sala.</p>
                </div>

                <!-- Detalles del Turno Programado -->
                <div v-else class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 text-left space-y-4">
                    <div class="flex items-center gap-3">
                        <CalendarIcon class="h-5 w-5 text-blue-500" />
                        <div>
                            <p class="text-xs text-muted-foreground dark:text-[#A1A09A]">Fecha</p>
                            <p class="font-bold capitalize">{{ formatDate(appointment.start_time!) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <ClockIcon class="h-5 w-5 text-blue-500" />
                        <div>
                            <p class="text-xs text-muted-foreground dark:text-[#A1A09A]">Hora</p>
                            <p class="font-bold">{{ formatTime(appointment.start_time!) }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t dark:border-[#3E3E3A] pt-6 text-left space-y-3">
                    <div class="flex items-center gap-2 text-sm">
                        <UserIcon class="h-4 w-4 text-muted-foreground" />
                        <span class="font-medium">{{ appointment.client.first_name }} {{ appointment.client.last_name }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <PhoneIcon class="h-4 w-4 text-muted-foreground" />
                        <span class="font-medium">{{ appointment.client.phone }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <div class="h-4 w-4 flex items-center justify-center">
                            <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                        </div>
                        <span class="font-medium">{{ appointment.service.name }}</span>
                    </div>
                </div>

                <div class="pt-4">
                    <Button as-child class="w-full h-12 text-lg">
                        <Link :href="route('home')">Finalizar</Link>
                    </Button>
                </div>
                
                <p class="text-[10px] text-muted-foreground dark:text-[#A1A09A]">Puedes tomar una captura de pantalla de este comprobante.</p>
            </div>
        </div>
    </div>
</template>
