<script setup lang="ts">
const lang = useLang();
const t = useT();
const { data } = await useFetch<any>(apiUrl('/auctions'));
const auctions = computed(() => data.value?.data || []);
</script>

<template>
  <main>
    <section class="page-hero">
      <div class="container">
        <p class="eyebrow">Live schedule</p>
        <h1>{{ t('nav.auctions') }}</h1>
        <p>Copart, IAAI and dealer auction windows in one operational board.</p>
      </div>
    </section>
    <section class="section">
      <div class="container">
        <div class="grid services-grid">
          <article v-for="auction in auctions" :key="auction.id" class="auction-card">
            <span class="badge">{{ auction.country }}</span>
            <h3>{{ auction.name }}</h3>
            <div class="auction-line"><span>Starts</span><strong>{{ auction.starts_at?.slice(0, 16).replace('T', ' ') }}</strong></div>
            <div class="auction-line"><span>Lots</span><strong>{{ auction.lots_count }}</strong></div>
            <NuxtLink class="btn ghost small" :to="{ path: `/${lang}/inventory`, query: { auction: auction.name.split(' ')[0] } }">
              View cars
            </NuxtLink>
          </article>
        </div>
      </div>
    </section>
  </main>
</template>
