<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import {
  Bike,
  Car,
  Footprints,
  MapPin,
  MapPinOff,
  Route,
  MapPinX,
  ArrowLeft
} from '@lucide/vue';
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import { Button } from '@/components/ui/button';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Input } from '@/components/ui/input';

const formRef = ref();
const mapContainer = ref<HTMLElement | null>(null);
let mapbox: any = null;
let map: any = null;
let originMarker: any = null;
let destMarker: any = null;

const selecting = ref<'none' | 'origin' | 'dest'>('none');

const form = reactive({
  originLat: null as number | null,
  originLng: null as number | null,
  destLat: null as number | null,
  destLng: null as number | null,
  transportMode: 'driving',
});

const enableSelectOrigin = () => (selecting.value = 'origin');
const enableSelectDest = () => (selecting.value = 'dest');
const disableSelect = () => (selecting.value = 'none');

const handleMapClick = (e: any) => {
  console.log(e);

  if (!mapbox) {
    return;
  }

  const { lat, lng } = e.lngLat;
  const marker = document.createElement('div');

  if (selecting.value === 'origin') {
    removeOriginMarker();
    form.originLat = Number(lat.toFixed(6));
    form.originLng = Number(lng.toFixed(6));

    if (originMarker) {
      originMarker.setLngLat(e.latlng);
    } else {
      marker.className = 'marker origin-marker';
      originMarker = new mapbox.Marker({ element: marker }).setLngLat(e.lngLat).addTo(map);
    }

    selecting.value = 'none';
  } else if (selecting.value === 'dest') {
    removeDestMarker();
    form.destLat = Number(lat.toFixed(6));
    form.destLng = Number(lng.toFixed(6));

    if (destMarker) {
      destMarker.setLngLat(e.latlng);
    } else {
      marker.className = 'marker dest-marker';
      destMarker = new mapbox.Marker({ element: marker }).setLngLat(e.lngLat).addTo(map);
    }

    selecting.value = 'none';
  }
};

const removeOriginMarker = () => {
  form.originLat = null;
  form.originLng = null;

  if (originMarker) {
    originMarker.remove();
    originMarker = null;
  }
};

const removeDestMarker = () => {
  form.destLat = null;
  form.destLng = null;

  if (destMarker) {
    destMarker.remove();
    destMarker = null;
  }
};

const handleFormReset = () => {
  removeOriginMarker();
  removeDestMarker();

  selecting.value = 'none';

  if (formRef.value) {
    formRef.value.reset();
  }
};

onMounted(async () => {
  // protección extra: si por alguna razón no hay window
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
    style: 'mapbox://styles/mapbox/streets-v12',
    center: [-74.088318, 4.672356],
    // config: {
    //     basemap: { theme: 'monochrome' }
    // },
    zoom: 10,
    projection: 'mercator',
  });

  map.on('click', handleMapClick);
});

onBeforeUnmount(() => {
  if (map) {
    map.off('click', handleMapClick);
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
    <div class="bg-white">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Nueva Ruta</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecciona origen y destino en el mapa.</p>
      </div>

      <div class="flex flex-col gap-6">
        <div>
          <div class="mb-3 flex gap-2">
            <Button
              type="button"
              variant="secondary"
              @click="enableSelectOrigin"
              :class="[selecting === 'origin' ? 'bg-blue-600 text-white' : '']"
            >
              <MapPin />
              Seleccionar Origen
            </Button>
            <Button
              type="button"
              variant="secondary"
              @click="enableSelectDest"
              :class="[selecting === 'dest' ? 'bg-emerald-600 text-white' : '']"
            >
              <MapPin />
              Seleccionar Destino
            </Button>
            <Button
              type="button"
              @click="disableSelect"
              variant="secondary"
              class="ml-auto"
            >
              <MapPinX />
              Cancelar selección
            </Button>
          </div>

          <div ref="mapContainer" class="h-80 w-full overflow-hidden rounded-lg border border-gray-200"></div>
          <p class="mt-2 text-xs text-gray-500">Haz click en el mapa para fijar el punto activo.</p>

          <div class="mt-4">
            <Tabs :default-value="form.transportMode" v-model="form.transportMode">
              <TabsList>
                <TabsTrigger value="driving-traffic">
                  <Car /> Traffic
                </TabsTrigger>
                <TabsTrigger value="driving">
                  <Car /> Driving
                </TabsTrigger>
                <TabsTrigger value="cycling">
                  <Bike /> Cycling
                </TabsTrigger>
                <TabsTrigger value="walking">
                  <Footprints /> Walking
                </TabsTrigger>
              </TabsList>
            </Tabs>
          </div>
        </div>

        <div>
          <Form ref="formRef" action="/routes" method="post" class="space-y-4">
            <input type="hidden" name="transport_mode" v-model="form.transportMode" />

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
              <h3 class="mb-2 text-sm font-semibold text-blue-600 uppercase">Punto de Origen</h3>
              <div class="grid grid-cols-2 gap-2">
                <Input
                  name="origin_lat"
                  :value="form.originLat"
                  type="text"
                  readonly
                  class="w-full focus-visible:ring-0 shadow-none bg-white"
                  placeholder="No seleccionado"
                />
                <Input
                  name="origin_lng"
                  :value="form.originLng"
                  type="text"
                  readonly
                  class="w-full focus-visible:ring-0 shadow-none bg-white"
                  placeholder="No seleccionado"
                />
              </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
              <h3 class="mb-2 text-sm font-semibold text-emerald-600 uppercase">Punto de Destino</h3>
              <div class="grid grid-cols-2 gap-2">
                <Input
                  name="destination_lat"
                  :value="form.destLat"
                  type="text"
                  readonly
                  class="w-full focus-visible:ring-0 shadow-none bg-white"
                  placeholder="No seleccionado"
                />
                <Input
                  name="destination_lng"
                  :value="form.destLng"
                  type="text"
                  readonly
                  class="w-full focus-visible:ring-0 shadow-none bg-white"
                  placeholder="No seleccionado"
                />
              </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
              <Button
                type="button"
                @click="handleFormReset"
                variant="secondary"
              >
                <MapPinOff />
                Limpiar
              </Button>

              <Button
                type="submit"
                :disabled="!(form.originLat && form.originLng && form.destLat && form.destLng)"
                class="bg-blue-600 hover:bg-blue-700"
                variant="default"
              >
                <Route />
                Optimizar Ruta
              </Button>
            </div>
          </Form>
        </div>
      </div>
    </div>
  </div>
</template>
