<script setup lang="ts">
const { data } = await useFetch<any>(apiUrl('/pages/about'));
const page = computed(() => data.value?.data);
</script>

<template>
  <main>
    <section class="page-hero">
      <div class="container">
        <p class="eyebrow">CarMark</p>
        <h1>{{ localize(page?.title) }}</h1>
      </div>
    </section>
    <section class="section">
      <div class="container content-panel content-body">
        <div v-html="localize(page?.body)" />
      </div>
    </section>
  </main>
</template>
