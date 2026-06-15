<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { 
    Dialog, 
    DialogContent, 
    DialogDescription, 
    DialogFooter, 
    DialogHeader, 
    DialogTitle 
} from '@/components/ui/dialog';
import { CalendarIcon, ClockIcon, UserIcon, PhoneIcon, ChevronRightIcon, CheckCircle2Icon, AlertCircleIcon } from 'lucide-vue-next';

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
const showRegisterModal = ref(false);
const showConfirmModal = ref(false);

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
        const response = await axios.post(route('booking.check-client'), {
            phone: form.phone 
        });
        const data = response.data;
        
        if (data.exists) {
            form.first_name = data.client.first_name;
            form.last_name = data.client.last_name;
            clientExists.value = true;
            form.client_exists = true;
            step.value = 3; // Si existe, saltamos directo a servicios
        } else {
            form.first_name = '';
            form.last_name = '';
            clientExists.value = false;
            form.client_exists = false;
            showRegisterModal.value = true; // Mostrar modal de registro
        }
    } catch (e) {
        console.error('Error verificando teléfono', e);
        showRegisterModal.value = true;
    }
};

const confirmRegister = () => {
    showRegisterModal.value = false;
    step.value = 2; // Ir a completar datos
};

const selectService = (service: Service) => {
    selectedService.value = service;
    form.service_id = service.id;
    if (service.type === 'sequential') {
        showConfirmModal.value = true;
    } else {
        step.value = 4;
    }
};

const submit = () => {
    form.post(route('booking.store'), {
        onSuccess: () => {
            showConfirmModal.value = false;
        }
    });
};
</script>

<template>
    <Head title="Reservar Turno" />

    <div class="min-h-screen bg-[#FDFDFC] p-4 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC] md:p-8">
        <div class="mx-auto max-w-2xl">
            <!-- Modales -->
            <Dialog v-model:open="showRegisterModal">
                <DialogContent class="sm:max-w-[425px] dark:bg-[#161615] dark:border-[#3E3E3A]">
                    <DialogHeader>
                        <DialogTitle class="flex items-center gap-2">
                            <AlertCircleIcon class="h-5 w-5 text-blue-500" />
                            Nuevo Cliente
                        </DialogTitle>
                        <DialogDescription class="pt-2">
                            No encontramos una cuenta asociada a este número. ¿Deseas proceder con el registro para solicitar tu turno?
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter class="mt-4">
                        <Button variant="outline" @click="showRegisterModal = false">Cancelar</Button>
                        <Button @click="confirmRegister">Registrarme</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <Dialog v-model:open="showConfirmModal">
                <DialogContent class="sm:max-w-[425px] dark:bg-[#161615] dark:border-[#3E3E3A]">
                    <DialogHeader>
                        <DialogTitle>Confirmar Reserva</DialogTitle>
                        <DialogDescription class="pt-2">
                            Estás por solicitar un turno para <strong>{{ selectedService?.name }}</strong>. 
                            Este servicio es por orden de llegada.
                        </DialogDescription>
                    </DialogHeader>
                    <div class="py-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Cliente:</span>
                            <span class="font-bold">{{ form.first_name }} {{ form.last_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Celular:</span>
                            <span class="font-bold">{{ form.phone }}</span>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button variant="outline" @click="showConfirmModal = false">Revisar</Button>
                        <Button @click="submit" :disabled="form.processing">
                            Confirmar y Finalizar
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <div class="mb-8 flex items-center justify-between">
                <Link :href="route('home')" class="text-sm font-medium hover:underline dark:text-[#EDEDEC]">← Volver al inicio</Link>
                <div class="flex gap-2">
                    <div v-for="i in 4" :key="i" 
                        class="h-1.5 w-8 rounded-full transition-colors"
                        :class="step >= i ? 'bg-blue-500' : 'bg-gray-200 dark:bg-[#3E3E3A]'"
                    ></div>
                </div>
            </div>

            <!-- PASO 1: Identificación -->
            <div v-if="step === 1" class="space-y-6">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Bienvenido</h1>
                    <p class="text-muted-foreground dark:text-[#A1A09A]">Ingresa tu número de celular para comenzar.</p>
                </div>
                
                <div class="rounded-xl border border-sidebar-border/70 bg-white p-8 shadow-sm dark:bg-[#161615] dark:border-[#3E3E3A]">
                    <div class="grid gap-4">
                        <Label for="phone" class="text-base dark:text-[#EDEDEC]">Número de Celular</Label>
                        <div class="relative">
                            <PhoneIcon class="absolute left-3 top-3 h-5 w-5 text-muted-foreground dark:text-[#A1A09A]" />
                            <Input 
                                id="phone" 
                                v-model="form.phone" 
                                class="pl-10 text-lg h-12 dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]" 
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

            <!-- PASO 2: Datos Personales -->
            <div v-if="step === 2" class="space-y-6">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Cuéntanos de ti</h1>
                    <p class="text-muted-foreground dark:text-[#A1A09A]">
                        Completa tu información por única vez para el sistema.
                    </p>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-white p-8 shadow-sm dark:bg-[#161615] dark:border-[#3E3E3A]">
                    <div class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="first_name" class="dark:text-[#EDEDEC]">Nombre</Label>
                            <Input id="first_name" v-model="form.first_name" class="h-11 dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="last_name" class="dark:text-[#EDEDEC]">Apellido</Label>
                            <Input id="last_name" v-model="form.last_name" class="h-11 dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]" />
                        </div>
                        <Button @click="step = 3" class="h-12 text-lg" :disabled="!form.first_name || !form.last_name">
                            Ver servicios disponibles
                        </Button>
                        <button @click="step = 1" class="text-sm text-muted-foreground hover:underline dark:text-[#A1A09A]">Cambiar número</button>
                    </div>
                </div>
            </div>

            <!-- PASO 3: Selección de Servicio -->
            <div v-if="step === 3" class="space-y-6">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">¿Qué necesitas?</h1>
                    <p class="text-muted-foreground dark:text-[#A1A09A]">Selecciona el servicio que deseas reservar.</p>
                </div>

                <div class="grid gap-4">
                    <div 
                        v-for="service in services" 
                        :key="service.id"
                        @click="selectService(service)"
                        class="group cursor-pointer rounded-xl border border-sidebar-border/70 bg-white p-6 transition-all hover:border-blue-500 hover:shadow-md dark:bg-[#161615] dark:border-[#3E3E3A] dark:hover:border-blue-500"
                    >
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <h3 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] group-hover:text-blue-500">{{ service.name }}</h3>
                                <p class="text-sm text-muted-foreground dark:text-[#A1A09A]">{{ service.description }}</p>
                                <div class="flex items-center gap-4 pt-2 text-xs font-medium">
                                    <span class="flex items-center gap-1 dark:text-[#EDEDEC]">
                                        <ClockIcon class="h-3 w-3" /> {{ service.duration_minutes }} min
                                    </span>
                                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ service.type === 'scheduled' ? 'Cita con horario' : 'Turno por llegada' }}
                                    </span>
                                </div>
                            </div>
                            <ChevronRightIcon class="h-6 w-6 text-gray-300 group-hover:text-blue-500 dark:text-[#3E3E3A]" />
                        </div>
                    </div>
                </div>
                <button @click="step = 1" class="text-sm text-muted-foreground hover:underline dark:text-[#A1A09A]">← Cambiar número o datos</button>
            </div>

            <!-- PASO 4: Confirmación para Reservas Programadas -->
            <div v-if="step === 4" class="space-y-6">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Programar Cita</h1>
                    <p class="text-muted-foreground dark:text-[#A1A09A]">Selecciona la fecha y hora para tu atención.</p>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-white p-8 shadow-sm dark:bg-[#161615] dark:border-[#3E3E3A]">
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <CalendarIcon class="h-8 w-8 text-blue-500" />
                            <div>
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Servicio: {{ selectedService?.name }}</p>
                                <p class="text-xs text-blue-700 dark:text-blue-400">{{ selectedService?.duration_minutes }} minutos de duración</p>
                            </div>
                        </div>

                        <div class="grid gap-6">
                            <div class="grid gap-2">
                                <Label class="dark:text-[#EDEDEC]">Fecha de la cita</Label>
                                <Input type="date" v-model="form.date" required class="dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]" />
                            </div>
                            <div class="grid gap-2">
                                <Label class="dark:text-[#EDEDEC]">Hora</Label>
                                <Input type="time" v-model="form.time" required class="dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]" />
                            </div>
                            <div class="grid gap-2">
                                <Label class="dark:text-[#EDEDEC]">Notas (opcional)</Label>
                                <Input v-model="form.notes" placeholder="Algún detalle adicional..." class="dark:bg-[#0a0a0a] dark:border-[#3E3E3A] dark:text-[#EDEDEC]" />
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <Button variant="outline" class="flex-1 h-12" @click="step = 3">Atrás</Button>
                            <Button @click="submit" class="flex-[2] h-12 text-lg" :disabled="form.processing || !form.date || !form.time">
                                Confirmar Reserva
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
