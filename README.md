# Guía de Aprendizaje: Sistema de Turnos (Vue 3 + Laravel)

Esta guía te ayudará a entender cómo está construido este proyecto y cómo puedes seguir expandiéndolo para aprender Vue 3, Inertia.js y Laravel.

## 🚀 Tecnologías Principales

El proyecto utiliza lo que se conoce como el **Stack VILT** (con algunas variaciones modernas):

1.  **Vue 3 (Composition API):** El framework de frontend. Usamos `<script setup>` y TypeScript.
2.  **Inertia.js:** El "pegamento" que permite crear SPAs (Single Page Applications) usando rutas y controladores de Laravel, sin necesidad de crear una API JSON.
3.  **Laravel 12:** El framework de backend.
4.  **Tailwind CSS:** Para el diseño visual mediante clases de utilidad.
5.  **TypeScript:** Para un código más seguro y con mejor autocompletado.

---

## 📂 Estructura del Frontend

Todo lo relacionado con Vue se encuentra en la carpeta `resources/js/`:

-   **`pages/`**: Aquí van las vistas completas (ej. `Welcome.vue`, `Dashboard.vue`). Cada archivo aquí corresponde a una "ruta" que devuelve Laravel.
-   **`components/`**: Componentes reutilizables (botones, inputs, layouts).
-   **`layouts/`**: Envuelven a las páginas para mantener elementos comunes como la barra de navegación.

---

## 🛠️ Cómo trabajar en el proyecto

### 1. Crear una nueva vista
1. Crea un archivo en `resources/js/pages/MiNuevaVista.vue`.
2. Define la lógica en Laravel (`routes/web.php`):
   ```php
   Route::get('/nueva-ruta', function () {
       return Inertia::render('MiNuevaVista', [
           'mensaje' => 'Hola desde Laravel'
       ]);
   });
   ```
3. Recibe los datos en Vue:
   ```vue
   <script setup lang="ts">
   defineProps<{ mensaje: string }>();
   </script>
   <template>
       <h1>{{ mensaje }}</h1>
   </template>
   ```

### 2. Librerías de Componentes instaladas
-   **Radix Vue:** Componentes sin estilos pero con accesibilidad (usados para modales, dropdowns, etc.).
-   **Lucide Vue Next:** Biblioteca de iconos. Ejemplo: `<CalendarIcon class="w-5 h-5" />`.
-   **VueUse:** Colección de utilidades esenciales para Vue (manejo de storage, sensores, etc.).

### 3. Comandos esenciales
-   `npm run dev`: Mantiene Vite activo para compilar tus cambios en Vue al instante.
-   `php artisan serve`: Levanta el servidor de Laravel (si no usas Herd).

---

## 🎓 Conceptos Clave para Aprender

1.  **Reactividad:** Aprende el uso de `ref()` y `reactive()` para datos que cambian la interfaz.
2.  **Props:** Cómo pasar datos de Laravel (o de un componente padre) a un componente hijo.
3.  **Events ($emit):** Cómo un hijo le comunica algo al padre.
4.  **Inertia Link:** Usa `<Link :href="route('nombre')">` en lugar de `<a>` para navegación instantánea sin recargar la página.

---

¡Disfruta aprendiendo! Si tienes dudas sobre un componente específico, pregúntame.
