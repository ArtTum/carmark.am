<script setup lang="ts">
const { data } = await useFetch<any>(apiUrl('/pages/how-to-buy'));
const page = computed(() => data.value?.data);
</script>

<template>
  <main>
    <section class="page-hero">
      <div class="container">
        <p class="eyebrow">Process</p>
        <h1>{{ localize(page?.title) }}</h1>
        <p>{{ localize(page?.body) }}</p>
      </div>
    </section>
    <section class="section">
      <div class="container grid services-grid">
        <article class="info-card"><h3>1. Choose</h3><p class="muted">Search inventory and save interesting lots.</p></article>
        <article class="info-card"><h3>2. Calculate</h3><p class="muted">Estimate bid, shipping and service fees before committing.</p></article>
        <article class="info-card"><h3>3. Bid</h3><p class="muted">Send a bid request or contact the team from the car page.</p></article>
        <article class="info-card"><h3>4. Receive</h3><p class="muted">Admin tracks lead status and customer communication.</p></article>
      </div>
    </section>
  </main>
</template>
