<script setup lang="ts">
const lang = useLang();
</script>

<template>
  <main class="auth-flow-screen">
    <section class="auth-flow-card">
      <div class="auth-flow-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </div>

      <h1>Գաղտնաբառը վերականգնվեց</h1>
      <p>Ձեր գաղտնաբառը հաջողությամբ վերականգնվել է։ Սեղմեք շարունակել մուտք գործելու համար</p>

      <NuxtLink class="auth-flow-submit auth-flow-link-submit" :to="`/${lang}/login`">
        Շարունակել
      </NuxtLink>

      <NuxtLink class="auth-flow-back" :to="`/${lang}/login`">
        <span aria-hidden="true">←</span>
        Վերադառնալ մուտք գործել
      </NuxtLink>
    </section>
  </main>
</template>
