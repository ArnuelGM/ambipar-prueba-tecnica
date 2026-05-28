<script setup lang="ts">
import { Form } from "@inertiajs/vue3"
import { ref } from "vue"
import AppLayout from '@/components/Layouts/AppLayout.vue';

const formRef = ref();

const form = {
    originLat: '4.734694',
    originLng: '-74.132336',
    destLat: '6.246687',
    destLng: '-75.576429'
}

const handleFormReset = () => {
    formRef.value.reset();
}

</script>

<template>
    <AppLayout>
        <div class="max-w-xl mx-auto p-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Nueva Ruta</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Introduce las coordenadas geográficas para calcular el trayecto más óptimo.
                    </p>
                </div>

                <Form ref="formRef" class="space-y-6" action="/routes" method="post">

                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                            <h3 class="text-sm font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">
                                Punto de Origen
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Latitud</label>
                                <input
                                    name="origin_lat" v-model="form.originLat"
                                    type="number" step="any" placeholder="Ej: 4.6097"
                                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Longitud</label>
                                <input
                                    name="origin_lng" v-model="form.originLng"
                                    type="number" step="any" placeholder="Ej: -74.0817"
                                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                Punto de Destino
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Latitud</label>
                                <input
                                    name="destination_lat" v-model="form.destLat"
                                    type="number" step="any" placeholder="Ej: 4.6784"
                                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Longitud</label>
                                <input
                                    name="destination_lng" v-model="form.destLng"
                                    type="number" step="any" placeholder="Ej: -74.0489"
                                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="handleFormReset"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            Limpiar
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-all flex items-center gap-2">
                            Optimizar Ruta
                        </button>
                    </div>

                </Form>

            </div>
        </div>
    </AppLayout>
</template>
