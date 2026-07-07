<script setup lang="ts">
const route = useRoute();
const isAdmin = computed(() => route.path.startsWith('/admin'));
</script>

<template>
  <div>
    <SiteHeader v-if="!isAdmin" />
    <NuxtPage />
    <SiteFooter v-if="!isAdmin" />
  </div>
</template>
