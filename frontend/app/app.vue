<script setup lang="ts">
const route = useRoute();
const isAdmin = computed(() => route.path.startsWith('/admin'));
const { initAuth } = useAuth();

await useSiteContent();

onMounted(initAuth);
</script>

<template>
  <div>
    <SiteHeader v-if="!isAdmin" />
    <NuxtPage />
    <SiteFooter v-if="!isAdmin" />
  </div>
</template>
