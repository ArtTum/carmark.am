<script setup lang="ts">
const { isLoading, progress } = useLoadingIndicator({
  throttle: 140,
  duration: 1800,
  hideDelay: 180,
  resetDelay: 260,
});

const displayedProgress = computed(() => Math.max(8, Math.min(100, Math.round(progress.value))));
</script>

<template>
  <Transition name="route-loader">
    <div v-if="isLoading" class="route-loader" role="status" aria-live="polite" aria-label="Loading page">
      <div class="route-loader__glow" />
      <div class="route-loader__card">
        <img class="route-loader__logo" src="/assets/carmark-logo.svg" alt="CarMark">
        <span class="route-loader__label">Loading page</span>
        <div class="route-loader__track" aria-hidden="true">
          <span class="route-loader__bar" :style="{ width: `${displayedProgress}%` }" />
        </div>
      </div>
    </div>
  </Transition>
</template>
