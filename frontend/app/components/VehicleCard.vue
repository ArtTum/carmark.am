<script setup lang="ts">
const props = defineProps<{ vehicle: any; wide?: boolean }>();
const t = useT();
const lang = useLang();
const watched = ref(false);

onMounted(() => {
  const ids = JSON.parse(localStorage.getItem('watchlist') || '[]');
  watched.value = ids.includes(props.vehicle.id);
});

function toggleWatch() {
  const ids = JSON.parse(localStorage.getItem('watchlist') || '[]');
  const next = ids.includes(props.vehicle.id) ? ids.filter((id: number) => id !== props.vehicle.id) : [...ids, props.vehicle.id];
  localStorage.setItem('watchlist', JSON.stringify(next));
  watched.value = next.includes(props.vehicle.id);
}
</script>

<template>
  <article class="car-card" :class="{ wide }">
    <div class="car-media">
      <NuxtLink :to="`/${lang}/cars/${vehicle.slug}`">
        <img :src="vehicle.images?.[0] || '/assets/extracted/03-17064740bf1e.jpg'" :alt="localize(vehicle.title)">
      </NuxtLink>
      <button class="watch-btn" :class="{ active: watched }" @click="toggleWatch">♡</button>
    </div>
    <div class="car-body">
      <h3><NuxtLink :to="`/${lang}/cars/${vehicle.slug}`">{{ localize(vehicle.title) }}</NuxtLink></h3>
      <p class="muted">{{ vehicle.lot }} · {{ vehicle.auction }}</p>
      <p>{{ vehicle.sale_date?.slice(0, 16).replace('T', ' ') }} · {{ vehicle.location }}</p>
      <div class="car-meta">
        <span>{{ vehicle.year }}</span>
        <span>{{ vehicle.body }}</span>
        <span>{{ vehicle.fuel }}</span>
      </div>
      <div class="car-actions">
        <strong>{{ money(vehicle.current_bid) }}</strong>
        <NuxtLink class="btn ghost small" :to="`/${lang}/cars/${vehicle.slug}`">{{ t('btn.details') }}</NuxtLink>
      </div>
    </div>
  </article>
</template>

