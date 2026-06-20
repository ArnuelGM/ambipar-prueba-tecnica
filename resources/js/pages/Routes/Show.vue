<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Route, Watch, MapPin, ArrowLeft, Car, Bike, Footprints } from '@lucide/vue';
import type { Ref } from 'vue';
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';

const props = defineProps({
  route: {
    type: Object,
    required: true,
  },
});

const transportMode: any = reactive({
  'driving-traffic': {
    icon: Car,
    label: 'Con tráfico',
  },
  driving: {
    icon: Car,
    label: 'Conducir',
  },
  cycling: {
    icon: Bike,
    label: 'Ciclismo',
  },
  walking: {
    icon: Footprints,
    label: 'Caminar',
  },
});

const mapContainer = ref<HTMLElement | null>(null);
let mapbox: any = null;
let map: any = null;
const selectedSectionIndex: Ref<string | number> = ref(-1);

const pathPoints = computed(() => {
  return props.route.path.data.coordinates;
});

const routePoints = computed(() => {
  const { sections } = props.route;
  const points: any[][] = [];
  sections.forEach((section: any) => {
    const {
      section_origin_lat,
      section_origin_lng,
      //section_destination_lat,
      //section_destination_lng
    } = section;
    const origin = [Number(section_origin_lng), Number(section_origin_lat)];
    //const dest = [Number(section_destination_lng), Number(section_destination_lat)]
    points.push(origin);
    //points.push(dest)
  });

  return points;
});

function findSectionPoints(section: any) {
  const points = [];
  let found_origin = false;
  let found_destination = false;

  for (const pathPoint of pathPoints.value) {
    const [pathPointLng, pathPointLat] = pathPoint;

    if (
      !found_origin &&
      +section.section_origin_lng === +pathPointLng &&
      +section.section_origin_lat === +pathPointLat
    ) {
      // Found origin
      found_origin = true;
      points.push([+pathPointLng, +pathPointLat]);
      continue;
    }

    if (
      !found_destination &&
      +section.section_destination_lng === +pathPointLng &&
      +section.section_destination_lat === +pathPointLat
    ) {
      // Found destination
      found_destination = true;
      points.push([+pathPointLng, +pathPointLat]);
      continue;
    }

    if (found_origin && found_destination) {
      break;
    }

    if (!found_origin && !found_destination) {
      continue;
    }

    if (found_origin && !found_destination) {
      points.push([+pathPointLng, +pathPointLat]);
    }
  }

  return points;
}

function viewSection(section: any) {
  if (!mapContainer.value) {
    return;
  }

  selectedSectionIndex.value = section.section_order;
  const points = findSectionPoints(section);
  const bounds = new mapbox.LngLatBounds();
  points.forEach(([lng, lat]) => {
    bounds.extend([lng, lat]);
  });
  map.fitBounds(bounds, { padding: 40 });
}

function buildBounds() {
  const bounds = new mapbox.LngLatBounds();
  routePoints.value.forEach((point) => {
    bounds.extend(point);
  });
  map.fitBounds(bounds, { padding: 20 });
}

function setOriginAndDestinationMarkers() {
  routePoints.value.slice(1, -1).forEach((lngLat) => {
    const marker = document.createElement('div');
    marker.className = 'marker route-marker';
    new mapbox.Marker({
      element: marker,
    })
      .setLngLat(lngLat)
      .addTo(map);
  });

  const originMarkerElement = document.createElement('div');
  const destMarkerElement = document.createElement('div');
  originMarkerElement.className = 'marker origin-marker';
  destMarkerElement.className = 'marker dest-marker';

  // Origin Marker
  const originLngLat = routePoints.value.at(0);
  new mapbox.Marker({
    element: originMarkerElement,
  })
    .setLngLat(originLngLat)
    .addTo(map);

  // Destination marker
  const destLngLat = routePoints.value.at(-1);
  new mapbox.Marker({
    element: destMarkerElement,
  })
    .setLngLat(destLngLat)
    .addTo(map);
}

async function drawRouteLine() {
  const geojson = {
    type: 'Feature',
    geometry: props.route.path.data,
  };
  map.on('load', () => {
    map.addSource('ruta', { type: 'geojson', data: geojson });
    map.addLayer({
      id: 'ruta-layer',
      type: 'line',
      source: 'ruta',
      layout: {
        'line-join': 'round',
        'line-cap': 'round',
      },
      paint: {
        'line-color': '#BF93E4',
        'line-width': 5,
      },
    });
  });
}

onMounted(async () => {
  if (typeof window === 'undefined') {
    return;
  }

  // import dinámico de Leaflet (evita ejecutar código en Node)
  mapbox = await import('mapbox-gl');
  mapbox = (await mapbox.default) ?? mapbox;
  mapbox.accessToken = import.meta.env.VITE_MAPBOX_TOKEN;

  // inicializa mapa (ajusta centro/zoom según necesites)
  map = new mapbox.Map({
    container: mapContainer.value as HTMLElement,
    style: 'mapbox://styles/mapbox/light-v11', // estilo base
    // config: {
    //     basemap: { theme: 'monochrome' }
    // },
    center: [-74.08, 4.6], // Bogotá
    zoom: 8,
    projection: 'mercator',
  });

  buildBounds();
  setOriginAndDestinationMarkers();
  drawRouteLine();
});

onBeforeUnmount(() => {
  if (map) {
    map.remove();
    map = null;
  }
});
</script>

<template>
  <div class="mx-auto max-w-5xl p-6">
    <div class="mb-4">
      <Link
        href="/routes"
        class="flex w-fit items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white"
      >
        <ArrowLeft />
        Volver al historial
      </Link>
    </div>

    <div class="relative space-y-6 bg-white dark:bg-gray-800">
      <div class="flex items-start justify-between border-b border-gray-100 pb-4 dark:border-gray-700">
        <div>
          <h2 class="text-xl font-bold text-gray-800 dark:text-white">Detalle de la Ruta #{{ route.id }}</h2>
          <p class="mt-1 text-xs text-gray-400">Calculada el {{ new Date(route.created_at).toLocaleString() }}</p>
        </div>
      </div>

      <div
        class="grid gap-4 rounded-xl border border-gray-200 bg-gray-50 p-4 grid-cols-2 dark:bg-gray-900/50"
      >
        <div>
          <span class="text-xs font-bold tracking-wide text-blue-600 uppercase dark:text-blue-400"
            >Punto de Partida</span
          >
          <p class="mt-0.5 text-sm font-medium text-gray-800 dark:text-gray-200">
            <MapPin class="mr-1 inline-block" :size="16" /> {{ route.origin_lat }},{{ route.origin_lng }}
          </p>
        </div>
        <div>
          <span class="text-xs font-bold tracking-wide text-emerald-600 uppercase dark:text-emerald-400"
            >Destino Final</span
          >
          <p class="mt-0.5 text-sm font-medium text-gray-800 dark:text-gray-200">
            <MapPin class="mr-1 inline-block" :size="16" /> {{ route.destination_lat }},{{ route.destination_lng }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <div class="rounded-xl border border-blue-100/70 bg-blue-50/60 p-4 dark:border-blue-900/50 dark:bg-blue-950/30">
          <p class="text-xs font-medium text-blue-600 dark:text-blue-400">Distancia de Viaje</p>
          <p class="mt-2 flex items-center text-2xl font-black text-blue-900 dark:text-blue-200">
            <Route class="mr-2 inline-block" /> {{ route.total_distance_km.toFixed(2) }} km
          </p>
        </div>
        <div
          class="rounded-xl border border-emerald-100/70 bg-emerald-50/60 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/30"
        >
          <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Tiempo Estimado</p>
          <p class="mt-2 flex items-center text-2xl font-black text-emerald-900 dark:text-emerald-200">
            <Watch class="mr-2 inline-block" /> {{ route.total_duration_minutes }} min
          </p>
        </div>
        <div
          class="sm:col-span-2 md:col-span-1 rounded-xl border border-amber-100/70 bg-amber-50/60 p-4 dark:border-amber-900/50 dark:bg-amber-950/30"
        >
          <p class="text-xs font-medium text-amber-600 dark:text-amber-400">Modo de Transporte</p>
          <p class="mt-2 flex items-center text-2xl font-black text-amber-900 dark:text-amber-200">
            <component :is="transportMode[route.transport_mode].icon" class="mr-2 inline-block" />
            {{ transportMode[route.transport_mode].label }}
          </p>
        </div>
      </div>

      <div class="sticky top-0 z-10 -my-6 bg-white py-6">
        <div
          ref="mapContainer"
          class="h-80 w-full overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700"
        ></div>
      </div>

      <div>
        <h4 class="mb-4 text-sm font-bold tracking-wider text-gray-800 uppercase dark:text-white">
          Instrucciones de Navegación ({{ route.sections?.length || 0 }})
        </h4>

        <div v-if="!route.sections || route.sections.length === 0" class="text-sm text-gray-400 italic">
          No se registraron tramos desglosados para esta ruta.
        </div>

        <div v-else class="relative ml-2.5 space-y-4 border-l-2 border-gray-200 dark:border-gray-700">
          <div v-for="section in route.sections" :key="section.id" class="relative pl-6">
            <span
              class="absolute top-2 -left-1.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-blue-600 dark:border-gray-800"
            ></span>

            <div
              @click="viewSection(section)"
              class="cursor-pointer rounded-lg border bg-gray-50/60 p-3 transition hover:border-blue-600 dark:border-gray-700/50 dark:bg-gray-900/20"
              :class="{
                'border-blue-600': selectedSectionIndex === section.section_order,
                'border-gray-100': selectedSectionIndex !== section.section_order,
              }"
            >
              <div class="mb-1 flex items-center justify-between text-xs">
                <span class="font-bold text-gray-400">TRAMO {{ section.section_order }}</span>
                <span class="font-medium text-gray-500"
                  >{{ section.distance_km.toFixed(2) }} km (aprox. {{ section.duration_minutes }} min)</span
                >
              </div>
              <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ section.instructions }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
