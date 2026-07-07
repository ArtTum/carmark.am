<script setup lang="ts">
const route = useRoute();
const isAdmin = computed(() => route.path.startsWith('/admin'));
const isAuthScreen = computed(() => /^\/[^/]+\/(login|register|forgot-password|check-email|reset-password|password-reset-success)(\/|$)/.test(route.path));
const { initAuth } = useAuth();

await useSiteContent();

onMounted(initAuth);
</script>

<template>
  <div>
    <SiteHeader v-if="!isAdmin && !isAuthScreen" />
    <NuxtPage />
    <SiteFooter v-if="!isAdmin && !isAuthScreen" />
  </div>
</template>
