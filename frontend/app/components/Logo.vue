<template>
  <NuxtLink class="brand" :to="to">
    <span class="brand-mark" />
    <span>carmark</span>
  </NuxtLink>
</template>

<script setup lang="ts">
withDefaults(defineProps<{ to?: string }>(), {
  to: '/hy',
});
</script>
