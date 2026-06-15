<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { CalendarIcon, ClockIcon, UserIcon, PhoneIcon, ChevronRightIcon, CheckCircle2Icon } from 'lucide-vue-next';

interface Service {
    id: number;
    name: string;
    description: string;
    type: 'sequential' | 'scheduled';
    duration_minutes: number;
}

const props = defineProps<{
    services: Service[];
}>();

const step = ref(1);
const selectedService = ref<Service | null>(null);
const clientExists = ref(false);

const form = useForm({
    phone: '',
    first_name: '',
    last_name: '',
    service_id: null as number | null,
    date: '',
    time: '',
    notes: '',
    client_exists: false,
});

const checkPhone = async () => {
    if (form.phone.length < 7) return;

    try {
        const response = await fetch(route('booking.check-client'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content,
            },
            body: JSON.stringify({ phone: form.phone }),
        });
        const data = await response.json();
        
        if (data.exists) {
            form.first_name = data.client.first_name;
            form.last_name = data.client.last_name;
            clientExists.ref = true;
            form.client_exists = true;
            step.ref = 2;
        } else {
            clientExists.ref = false;
            form.client_exists = false;
            step.ref = 2;
        }
    } catch (e) {
        console.error('Error verificando teléfono', e);
        step.ref = 2;
    }
};

const selectService = (service: Service) => {
    selectedService.value = service;
    form.service_id = service.id;
    step.value = 3;
};

const submit = () => {
    form.post(route('booking.store'));
};
</script>

<template>
    <Head title="Reservar Turno" />

    <div class="min-h-screen bg-[#FDFDFC] p-4 text-[#1b1b18] dark:bg-[#0a0a0a] md:p-8">
        <div class="mx-auto max-w-2xl">
            <div class="mb-8 flex items-center justify-between">
                <Link :href="route('home')" class="text-sm font-medium hover:underline">← Volver al inicio</Link>
                <div class="flex gap-2">
                    <div v-for="i in 4" :key="i" 
                        class="h-1.5 w-8 rounded-full transition-colors"
                        :class="step >= i ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-800'"
                    ></div>
                </div>
            </div>

            <!-- PASO 1: Identificación -->
            <div v-if="step === 1" class="space-y-6">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold">Bienvenido</h1>
                    <p class="text-muted-foreground">Ingresa tu número de celular para comenzar.</p>
                </div>
                
                <div class="rounded-xl border border-sidebar-border/70 bg-white p-8 shadow-sm dark:bg-[#161615]">
                    <div class="grid gap-4">
                        <Label for="phone" class="text-base">Número de Celular</Label>
                        <div class="relative">
                            <PhoneIcon class="absolute left-3 top-3 h-5 w-5 text-muted-foreground" />
                            <Input 
                                id="phone" 
                                v-model="form.phone" 
                                class="pl-10 text-lg h-12" 
                                placeholder="Ej: 3001234567"
                                @keyup.enter="checkPhone"
                            />
                        </div>
                        <Button @click="checkPhone" class="h-12 text-lg" :disabled="!form.phone">
                            Continuar
                            <ChevronRightIcon class="ml-2 h-5 w-5" />
                        </Button>
                    </div>
                </div>
            </div>

            <!-- PASO 2: Datos Personales (solo si es nuevo) -->
            <div v-if="step === 2" class="space-y-6">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold">{{ clientExists ? '¡Hola de nuevo!' : 'Cuéntanos de ti' }}</h1>
                    <p class="text-muted-foreground">
                        {{ clientExists ? 'Confirma tus datos para continuar.' : 'Completa tu información por única vez.' }}
                    </p>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-white p-8 shadow-sm dark:bg-[#161615]">
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="first_name">Nombre</Label>
                            <Input id="first_name" v-model="form.first_name" :disabled="clientExists" class="h-11" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="last_name">Apellido</Label>
                            <Input id="last_name" v-model="form.last_name" :disabled="clientExists" class="h-11" />
                        </div>
                        <Button @click="step = 3" class="h-12 text-lg">
                            Ver servicios disponibles
                        </Button>
                        <button @click="step = 1" class="text-sm text-muted-foreground hover:underline">Cambiar número</button>
                    </div>
                </div>
            </div>

            <!-- PASO 3: Selección de Servicio -->
            <div v-if="step === 3" class="space-y-6">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold">¿Qué necesitas?</h1>
                    <p class="text-muted-foreground">Selecciona el servicio que deseas reservar.</p>
                </div>

                <div class="grid gap-4">
                    <div 
                        v-for="service in services" 
                        :key="service.id"
                        @click="selectService(service)"
                        class="group cursor-pointer rounded-xl border border-sidebar-border/70 bg-white p-6 transition-all hover:border-blue-500 hover:shadow-md dark:bg-[#161615]"
                    >
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <h3 class="text-xl font-bold group-hover:text-blue-500">{{ service.name }}</h3>
                                <p class="text-sm text-muted-foreground">{{ service.description }}</p>
                                <div class="flex items-center gap-4 pt-2 text-xs font-medium">
                                    <span class="flex items-center gap-1">
                                        <ClockIcon class="h-3 w-3" /> {{ service.duration_minutes }} min
                                    </span>
                                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ service.type === 'scheduled' ? 'Cita con horario' : 'Turno por llegada' }}
                                    </span>
                                </div>
                            </div>
                            <ChevronRightIcon class="h-6 w-6 text-gray-300 group-hover:text-blue-500" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASO 4: Confirmación Final -->
            <div v-if="step === 4 || (selectedService?.type === 'sequential' && step === 3)" class="space-y-6">
                <!-- Para Secuencial se confirma directo, para Programado pediría fecha (simplificado aquí) -->
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold">Confirmar Reserva</h1>
                    <p class="text-muted-foreground">Revisa los detalles antes de finalizar.</p>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-white p-8 shadow-sm dark:bg-[#161615]">
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <CheckCircle2Icon class="h-8 w-8 text-blue-500" />
                            <div>
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Servicio seleccionado</p>
                                <p class="text-lg font-bold">{{ selectedService?.name }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 text-sm">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-muted-foreground">Cliente:</span>
                                <span class="font-bold">{{ form.first_name }} {{ form.last_name }}</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-muted-foreground">Celular:</span>
                                <span class="font-bold">{{ form.phone }}</span>
                            </div>
                            <div v-if="selectedService?.type === 'scheduled'" class="grid gap-4 pt-2">
                                <div class="grid gap-2">
                                    <Label>Fecha</Label>
                                    <Input type="date" v-model="form.date" required />
                                </div>
                                <div class="grid gap-2">
                                    <Label>Hora</Label>
                                    <Input type="time" v-model="form.time" required />
                                </div>
                            </div>
                        </div>

                        <Button @click="submit" class="h-12 w-full text-lg" :disabled="form.processing">
                            Confirmar Turno
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
