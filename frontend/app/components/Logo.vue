<template>
  <NuxtLink class="brand" :to="to">
    <img src="/assets/carmark-logo.svg" alt="CarMark">
  </NuxtLink>
</template>

<script setup lang="ts">
withDefaults(defineProps<{ to?: string }>(), {
  to: '/hy',
});
</script>
