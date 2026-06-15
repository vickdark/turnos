<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Bienvenido">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] lg:justify-center lg:p-8">
        <header class="not-has-[nav]:hidden mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Panel de Control
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Iniciar sesión
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Registrarse
                    </Link>
                </template>
            </nav>
        </header>
        <div class="duration-750 starting:opacity-0 flex w-full items-center justify-center opacity-100 transition-opacity lg:grow">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div
                    class="flex-1 rounded-bl-lg rounded-br-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] lg:rounded-br-none lg:rounded-tl-lg lg:p-20"
                >
                    <h1 class="mb-4 text-2xl font-bold">Gestión de Turnos simplificada</h1>
                    <p class="mb-6 text-base text-[#706f6c] dark:text-[#A1A09A]">
                        Organiza tus citas de manera eficiente y profesional. <br />Nuestra plataforma te permite gestionar turnos con facilidad.
                    </p>
                    
                    <div class="mb-8 grid grid-cols-1 gap-4 text-sm lg:grid-cols-2">
                        <div class="flex items-start gap-3">
                            <div class="mt-1 h-2 w-2 rounded-full bg-blue-500"></div>
                            <p>Reserva turnos en segundos desde cualquier dispositivo.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-1 h-2 w-2 rounded-full bg-green-500"></div>
                            <p>Recibe confirmaciones y recordatorios automáticos.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-1 h-2 w-2 rounded-full bg-purple-500"></div>
                            <p>Gestiona tu disponibilidad de forma flexible.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-1 h-2 w-2 rounded-full bg-orange-500"></div>
                            <p>Historial completo de citas y pacientes/clientes.</p>
                        </div>
                    </div>

                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <Link
                                :href="route('booking.index')"
                                class="inline-block rounded-sm border border-black bg-[#1b1b18] px-8 py-2 text-sm leading-normal text-white hover:border-black hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:border-white dark:hover:bg-white"
                            >
                                Reservar Turno
                            </Link>
                        </li>
                    </ul>
                </div>
                <div
                    class="relative -mb-px aspect-[335/376] w-full shrink-0 overflow-hidden rounded-t-lg bg-blue-50 dark:bg-blue-950 lg:-ml-px lg:mb-0 lg:aspect-auto lg:w-[438px] lg:rounded-r-lg lg:rounded-t-none"
                >
                    <div class="flex h-full items-center justify-center p-12">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-48 w-48 text-blue-500 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
