<script setup lang="ts">
const t = useT();
const lang = useLang();
const query = ref('');
const heroStyle = { '--hero-image': "url('/assets/extracted/06-075d3f1ebdf3.jpg')" } as Record<string, string>;

const { data: vehiclesData } = await useFetch<any>(apiUrl('/vehicles'));
const { data: auctionsData } = await useFetch<any>(apiUrl('/auctions'));

const vehicles = computed(() => vehiclesData.value?.data || []);
const featured = computed(() => vehicles.value.filter((item: any) => item.featured).slice(0, 6));
const privateCars = computed(() => vehicles.value.filter((item: any) => item.private_sale).slice(0, 3));
const auctions = computed(() => auctionsData.value?.data || []);
const brands = computed(() => [...new Set(vehicles.value.map((item: any) => item.make))].slice(0, 8));

function search() {
  return navigateTo({ path: `/${lang.value}/inventory`, query: query.value ? { q: query.value } : {} });
}
</script>

<template>
  <main>
    <section class="hero" :style="heroStyle">
      <div class="container hero-inner">
        <div>
          <p class="eyebrow">Copart, IAAI, Manheim</p>
          <h1>{{ t('hero.title') }}</h1>
          <p class="hero-copy">{{ t('hero.copy') }}</p>
          <form class="search-panel" @submit.prevent="search">
            <input v-model="query" type="search" :placeholder="t('search.placeholder')">
            <button class="btn primary">{{ t('btn.search') }}</button>
          </form>
          <div class="hero-stats">
            <div class="stat"><strong>24/7</strong><span>Bid support</span></div>
            <div class="stat"><strong>3</strong><span>Languages</span></div>
            <div class="stat"><strong>100%</strong><span>Transparent fees</span></div>
          </div>
        </div>
        <img class="hero-car" src="/assets/extracted/17-28948be78c64.png" alt="Mazda auction car">
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="section-head">
          <div>
            <p class="eyebrow">Inventory</p>
            <h2>{{ t('home.featured') }}</h2>
          </div>
          <NuxtLink class="btn ghost" :to="`/${lang}/inventory`">{{ t('inventory.title') }}</NuxtLink>
        </div>
        <div class="grid cars-grid">
          <VehicleCard v-for="vehicle in featured" :key="vehicle.id" :vehicle="vehicle" />
        </div>
      </div>
    </section>

    <section class="section soft">
      <div class="container">
        <div class="section-head">
          <div>
            <p class="eyebrow">Live schedule</p>
            <h2>{{ t('home.auctions') }}</h2>
          </div>
          <NuxtLink class="btn ghost" :to="`/${lang}/auctions`">{{ t('nav.auctions') }}</NuxtLink>
        </div>
        <div class="grid services-grid">
          <article v-for="auction in auctions" :key="auction.id" class="auction-card">
            <span class="badge">{{ auction.country }}</span>
            <h3>{{ auction.name }}</h3>
            <div class="auction-line"><span>Starts</span><strong>{{ auction.starts_at?.slice(0, 16).replace('T', ' ') }}</strong></div>
            <div class="auction-line"><span>Lots</span><strong>{{ auction.lots_count }}</strong></div>
          </article>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="section-head">
          <div>
            <p class="eyebrow">Direct sale</p>
            <h2>{{ t('home.private') }}</h2>
          </div>
        </div>
        <div class="grid cars-grid">
          <VehicleCard v-for="vehicle in privateCars" :key="vehicle.id" :vehicle="vehicle" />
        </div>
      </div>
    </section>

    <section class="section soft">
      <div class="container">
        <div class="section-head">
          <div>
            <p class="eyebrow">Browse faster</p>
            <h2>{{ t('home.brands') }}</h2>
          </div>
        </div>
        <div class="grid brands-grid">
          <NuxtLink v-for="brand in brands" :key="brand" class="brand-chip" :to="{ path: `/${lang}/inventory`, query: { make: brand } }">
            <span>{{ brand }}</span>
            <span>→</span>
          </NuxtLink>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="section-head">
          <div>
            <p class="eyebrow">Workflow</p>
            <h2>{{ t('home.services') }}</h2>
          </div>
        </div>
        <div class="grid services-grid">
          <article class="info-card"><h3>Search</h3><p class="muted">Find the right lot with filters, auction data and saved vehicles.</p></article>
          <article class="info-card"><h3>Bid</h3><p class="muted">Send a bid request and let the admin team manage the next step.</p></article>
          <article class="info-card"><h3>Ship</h3><p class="muted">Calculate local fees, shipping and final landed price before buying.</p></article>
          <article class="info-card"><h3>Deliver</h3><p class="muted">Track leads, documents and customer follow-up from the admin panel.</p></article>
        </div>
      </div>
    </section>
  </main>
</template>
