<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  route: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="max-w-3xl mx-auto p-6">

    <div class="mb-4">
      <Link href="/routes"
        class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white flex items-center gap-1 w-fit"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Volver al historial
      </Link>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
      <div class="flex justify-between items-start border-b border-gray-100 dark:border-gray-700 pb-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800 dark:text-white">Detalle de la Ruta #{{ route.id }}</h2>
          <p class="text-xs text-gray-400 mt-1">Calculada el {{ new Date(route.created_at).toLocaleString() }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl">
        <div>
          <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Punto de Partida</span>
          <p class="text-gray-800 dark:text-gray-200 font-medium text-sm mt-0.5">{{ route.origin_lat }},{{ route.origin_lng }}</p>
        </div>
        <div>
          <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Destino Final</span>
          <p class="text-gray-800 dark:text-gray-200 font-medium text-sm mt-0.5">{{ route.destination_lat }},{{ route.destination_lng }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="bg-blue-50/60 dark:bg-blue-950/30 p-4 rounded-xl border border-blue-100/70 dark:border-blue-900/50">
          <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Distancia de Viaje</p>
          <p class="text-2xl font-black text-blue-900 dark:text-blue-200">{{ route.total_distance_km.toFixed(2) }} km</p>
        </div>
        <div class="bg-emerald-50/60 dark:bg-emerald-950/30 p-4 rounded-xl border border-emerald-100/70 dark:border-emerald-900/50">
          <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Tiempo Estimado</p>
          <p class="text-2xl font-black text-emerald-900 dark:text-emerald-200">{{ route.total_duration_minutes }} min</p>
        </div>
      </div>

      <div>
        <h4 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider mb-4">
          Instrucciones de Navegación ({{ route.sections?.length || 0 }})
        </h4>

        <div v-if="!route.sections || route.sections.length === 0" class="text-sm text-gray-400 italic">
          No se registraron tramos desglosados para esta ruta.
        </div>

        <div v-else class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-2.5 space-y-4">
          <div
            v-for="section in route.sections"
            :key="section.id"
            class="relative pl-6"
          >
            <span class="absolute -left-[6px] top-2 bg-blue-600 w-2.5 h-2.5 rounded-full border-2 border-white dark:border-gray-800"></span>

            <div class="bg-gray-50/60 dark:bg-gray-900/20 p-3 rounded-lg border border-gray-100 dark:border-gray-700/50">
              <div class="flex items-center justify-between text-xs mb-1">
                <span class="font-bold text-gray-400">TRAMO {{ section.section_order }}</span>
                <span class="text-gray-500 font-medium">{{ section.distance_km.toFixed(2) }} km (aprox. {{ section.duration_minutes }} min)</span>
              </div>
              <p class="text-gray-700 dark:text-gray-300 text-sm">
                {{ section.instructions }}
              </p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>
