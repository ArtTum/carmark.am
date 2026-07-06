<script setup lang="ts">
const t = useT();
const lang = useLang();
const route = useRoute();

const form = reactive({
  q: String(route.query.q || ''),
  make: String(route.query.make || ''),
  model: String(route.query.model || ''),
  body: String(route.query.body || ''),
  auction: String(route.query.auction || ''),
  sort: String(route.query.sort || ''),
});

const cleanQuery = computed(() => Object.fromEntries(Object.entries(form).filter(([, value]) => value)));
const { data, pending, refresh } = await useFetch<any>(apiUrl('/vehicles'), { query: cleanQuery });

watch(() => route.query, (query) => {
  Object.assign(form, {
    q: String(query.q || ''),
    make: String(query.make || ''),
    model: String(query.model || ''),
    body: String(query.body || ''),
    auction: String(query.auction || ''),
    sort: String(query.sort || ''),
  });
}, { deep: true });

watch(cleanQuery, () => refresh(), { deep: true });

const vehicles = computed(() => data.value?.data || []);
const filters = computed(() => data.value?.filters || {});

function applyFilters() {
  return navigateTo({ path: `/${lang.value}/inventory`, query: cleanQuery.value });
}

function resetFilters() {
  Object.assign(form, { q: '', make: '', model: '', body: '', auction: '', sort: '' });
  return applyFilters();
}
</script>

<template>
  <main>
    <section class="page-hero">
      <div class="container">
        <p class="eyebrow">Auction cars</p>
        <h1>{{ t('inventory.title') }}</h1>
        <p>{{ t('hero.copy') }}</p>
      </div>
    </section>

    <section class="section">
      <div class="container inventory-layout">
        <aside class="form-panel filters">
          <h2>{{ t('inventory.filters') }}</h2>
          <form @submit.prevent="applyFilters">
            <label class="field">
              <span>Search</span>
              <input v-model="form.q" type="search" :placeholder="t('search.placeholder')">
            </label>
            <label class="field">
              <span>Make</span>
              <select v-model="form.make">
                <option value="">All makes</option>
                <option v-for="make in filters.makes" :key="make" :value="make">{{ make }}</option>
              </select>
            </label>
            <label class="field">
              <span>Body</span>
              <select v-model="form.body">
                <option value="">All bodies</option>
                <option v-for="body in filters.bodies" :key="body" :value="body">{{ body }}</option>
              </select>
            </label>
            <label class="field">
              <span>Auction</span>
              <select v-model="form.auction">
                <option value="">All auctions</option>
                <option v-for="auction in filters.auctions" :key="auction" :value="auction">{{ auction }}</option>
              </select>
            </label>
            <label class="field">
              <span>Sort</span>
              <select v-model="form.sort">
                <option value="">Newest first</option>
                <option value="price">Lowest price</option>
              </select>
            </label>
            <button class="btn primary full">{{ t('btn.search') }}</button>
            <button class="btn ghost full" type="button" @click="resetFilters">Reset</button>
          </form>
        </aside>

        <div>
          <div class="section-head">
            <div>
              <p class="eyebrow">{{ vehicles.length }} results</p>
              <h2>{{ t('nav.buy') }}</h2>
            </div>
          </div>
          <p v-if="pending" class="muted">Loading...</p>
          <div class="grid">
            <VehicleCard v-for="vehicle in vehicles" :key="vehicle.id" :vehicle="vehicle" wide />
          </div>
        </div>
      </div>
    </section>
  </main>
</template>
