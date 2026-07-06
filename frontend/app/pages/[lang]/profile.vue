<script setup lang="ts">
const watchedIds = ref<number[]>([]);
const { data } = await useFetch<any>(apiUrl('/vehicles'));
const vehicles = computed(() => data.value?.data || []);
const watched = computed(() => vehicles.value.filter((vehicle: any) => watchedIds.value.includes(vehicle.id)));

onMounted(() => {
  watchedIds.value = JSON.parse(localStorage.getItem('watchlist') || '[]');
});
</script>

<template>
  <main>
    <section class="page-hero">
      <div class="container">
        <p class="eyebrow">Saved cars</p>
        <h1>Account</h1>
        <p>Your saved vehicle list is stored locally in this browser.</p>
      </div>
    </section>
    <section class="section">
      <div class="container">
        <div v-if="watched.length" class="grid cars-grid">
          <VehicleCard v-for="vehicle in watched" :key="vehicle.id" :vehicle="vehicle" />
        </div>
        <div v-else class="content-panel">
          <h2>No saved cars yet</h2>
          <p class="muted">Use the heart button on vehicle cards to build a shortlist.</p>
        </div>
      </div>
    </section>
  </main>
</template>
