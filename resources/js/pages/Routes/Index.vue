<script setup>
import { Link } from '@inertiajs/vue3'
//import { show } from "@/"

defineProps({
  routes: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <div class="max-w-5xl mx-auto p-6">
    <div class="mb-8 border-b border-gray-100 dark:border-gray-700 pb-4 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Historial de Rutas</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Selecciona un trayecto para auditar su desglose completo de tramos.
            </p>
        </div>
        <Link href="/routes/create" class="px-4 py-2 text-white bg-blue-600 rounded">Nueva Ruta</Link>
    </div>

    <div v-if="routes.length === 0" class="bg-white dark:bg-gray-800 p-12 rounded-xl border text-center text-gray-400">
      No hay rutas optimizadas registradas en el sistema.
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="route in routes"
        :key="route.id"
        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between"
      >
        <div>
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold px-2 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded">
              ID: #{{ route.id }}
            </span>
            <span class="text-xs text-gray-400">
              {{ new Date(route.created_at).toLocaleDateString() }}
            </span>
          </div>

          <div class="space-y-3">
            <div class="flex items-start gap-2.5 text-sm text-gray-700 dark:text-gray-300">
              <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-1 shrink-0"></span>
              <p class="font-medium line-clamp-2"><span class="text-xs text-gray-400 block font-normal">
                Origen:</span> {{ route.origin_lat }},{{ route.origin_lng }}
                </p>
            </div>
            <div class="flex items-start gap-2.5 text-sm text-gray-700 dark:text-gray-300">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1 shrink-0"></span>
              <p class="font-medium line-clamp-2"><span class="text-xs text-gray-400 block font-normal">
                Destino:</span> {{ route.destination_lat }},{{ route.destination_lng }}
                </p>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
          <div class="text-xs font-semibold text-gray-500 space-x-3">
            <span>{{ route.total_distance_km.toFixed(1) }} km</span>
            <span>•</span>
            <span>{{ route.total_duration_minutes }} min</span>
          </div>

          <Link
            :href="`/routes/${route.id}`"
            class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1"
          >
            Ver tramos
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
