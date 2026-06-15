<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { onMounted, onUnmounted, computed } from 'vue';
import { Volume2Icon, UserIcon, ListOrderedIcon, ClockIcon } from 'lucide-vue-next';

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
    };
}

const props = defineProps<{
    appointments: Appointment[];
}>();

// El primer turno de la lista es el que se está llamando o es el siguiente
const currentAppointment = computed(() => props.appointments[0] || null);
const waitingAppointments = computed(() => props.appointments.slice(1, 6)); // Siguientes 5

// Auto-refresco cada 10 segundos para ver nuevos turnos o cambios de estado
let refreshInterval: any;

onMounted(() => {
    refreshInterval = setInterval(() => {
        router.reload({ only: ['appointments'] });
    }, 10000);
});

onUnmounted(() => {
    clearInterval(refreshInterval);
});

const formatTime = (dateStr: string) => {
    return new Date(dateStr).toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Monitor de Turnos" />

    <div class="min-h-screen bg-[#0a0a0a] text-white p-8 font-sans overflow-hidden flex flex-col gap-8">
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-gray-800 pb-6">
            <div class="flex items-center gap-4">
                <div class="bg-blue-600 p-3 rounded-xl">
                    <Volume2Icon class="h-8 w-8 text-white" />
                </div>
                <h1 class="text-4xl font-black uppercase tracking-tighter">Sala de Espera</h1>
            </div>
            <div class="text-right">
                <p class="text-6xl font-mono">{{ new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}</p>
                <p class="text-gray-400 font-medium">{{ new Date().toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' }) }}</p>
            </div>
        </div>

        <div class="flex-1 grid grid-cols-12 gap-8">
            <!-- Llamado Principal (Izquierda) -->
            <div class="col-span-8 flex flex-col gap-6">
                <div class="flex-1 bg-gradient-to-br from-blue-700 to-blue-900 rounded-3xl p-12 flex flex-col justify-center items-center text-center shadow-2xl border-4 border-blue-500/30">
                    <template v-if="currentAppointment">
                        <span class="text-blue-200 text-2xl font-bold uppercase tracking-[0.2em] mb-4">Llamando a:</span>
                        <h2 class="text-9xl font-black mb-8 drop-shadow-lg">
                            {{ currentAppointment.service.type === 'sequential' ? 'T-' + currentAppointment.sequence_number : formatTime(currentAppointment.start_time!) }}
                        </h2>
                        <div class="bg-white/10 backdrop-blur-md px-12 py-6 rounded-2xl border border-white/20">
                            <p class="text-5xl font-bold uppercase tracking-tight">{{ currentAppointment.client.first_name }} {{ currentAppointment.client.last_name }}</p>
                            <p class="text-2xl text-blue-300 mt-2 font-medium">{{ currentAppointment.service.name }}</p>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-4xl text-blue-200 font-medium italic">No hay turnos pendientes</p>
                    </template>
                </div>
            </div>

            <!-- Siguientes Turnos (Derecha) -->
            <div class="col-span-4 flex flex-col gap-6">
                <div class="bg-gray-900/50 rounded-3xl p-8 border border-gray-800 flex-1">
                    <h3 class="text-2xl font-bold mb-8 flex items-center gap-3 text-gray-400">
                        <ClockIcon class="h-6 w-6" />
                        Siguientes en turno
                    </h3>
                    
                    <div class="space-y-4">
                        <div 
                            v-for="app in waitingAppointments" 
                            :key="app.id"
                            class="bg-gray-800/40 border border-gray-700/50 p-6 rounded-2xl flex justify-between items-center transition-all hover:bg-gray-800"
                        >
                            <div class="flex flex-col">
                                <span class="text-3xl font-black text-blue-400">
                                    {{ app.service.type === 'sequential' ? 'T-' + app.sequence_number : formatTime(app.start_time!) }}
                                </span>
                                <span class="text-lg font-bold text-gray-300 uppercase truncate max-w-[180px]">
                                    {{ app.client.first_name }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold bg-gray-700 px-3 py-1 rounded-full text-gray-400 uppercase">
                                    {{ app.service.name }}
                                </span>
                            </div>
                        </div>
                        
                        <div v-if="waitingAppointments.length === 0 && currentAppointment" class="text-center py-12">
                            <p class="text-gray-600 italic">No hay más turnos en espera</p>
                        </div>
                    </div>
                </div>

                <!-- Footer del Monitor -->
                <div class="bg-blue-600 rounded-2xl p-6 flex items-center justify-center text-center">
                    <p class="text-xl font-bold italic">"Su bienestar es nuestra prioridad"</p>
                </div>
            </div>
        </div>

        <!-- Barra de Noticias / Información inferior -->
        <div class="bg-gray-900 border border-gray-800 p-4 rounded-xl flex items-center gap-6 overflow-hidden">
            <div class="bg-red-600 px-4 py-1 rounded font-bold animate-pulse">AVISO</div>
            <p class="text-lg font-medium whitespace-nowrap animate-marquee">
                Bienvenido a nuestro sistema de turnos. Por favor, esté atento a la pantalla para su llamado. • Identifíquese con su número de celular al llegar. • Gracias por su paciencia.
            </p>
        </div>
    </div>
</template>

<style scoped>
.animate-marquee {
    animation: marquee 30s linear infinite;
}

@keyframes marquee {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}
</style>
