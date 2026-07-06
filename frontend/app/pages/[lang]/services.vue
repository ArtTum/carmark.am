<script setup lang="ts">
const { data } = await useFetch<any>(apiUrl('/pages/services'));
const page = computed(() => data.value?.data);
</script>

<template>
  <main>
    <section class="page-hero">
      <div class="container">
        <p class="eyebrow">Services</p>
        <h1>{{ localize(page?.title) }}</h1>
        <p>{{ localize(page?.body) }}</p>
      </div>
    </section>
    <section class="section">
      <div class="container grid services-grid">
        <article class="info-card"><h3>Auction access</h3><p class="muted">Lot research, history checks and bid coordination.</p></article>
        <article class="info-card"><h3>Payment workflow</h3><p class="muted">Invoice, fee and customer status tracking from admin.</p></article>
        <article class="info-card"><h3>Shipping</h3><p class="muted">Route, port and local delivery fee planning.</p></article>
        <article class="info-card"><h3>Documents</h3><p class="muted">Buyer requests, lead notes and content controlled in English admin.</p></article>
      </div>
    </section>
  </main>
</template>
