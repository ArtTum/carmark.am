<script setup lang="ts">
const t = useT();
const lang = useLang();
const route = useRoute();

const switchLang = (code: string) => {
  const parts = route.path.split('/').filter(Boolean);
  if (['hy', 'en', 'ru'].includes(parts[0])) parts[0] = code;
  else parts.unshift(code);
  return `/${parts.join('/')}`;
};
</script>

<template>
  <header class="site-header">
    <div class="container header-inner">
      <Logo :to="`/${lang}`" />
      <nav class="main-nav">
        <NuxtLink :to="`/${lang}/inventory`">{{ t('nav.buy') }}</NuxtLink>
        <NuxtLink :to="`/${lang}/auctions`">{{ t('nav.auctions') }}</NuxtLink>
        <NuxtLink :to="`/${lang}/services`">{{ t('nav.services') }}</NuxtLink>
        <NuxtLink :to="`/${lang}/how-to-buy`">{{ t('nav.how') }}</NuxtLink>
        <NuxtLink :to="`/${lang}/contact`">{{ t('nav.contact') }}</NuxtLink>
      </nav>
      <div class="header-actions">
        <div class="lang-switch">
          <NuxtLink v-for="code in ['hy', 'en', 'ru']" :key="code" :class="{ active: code === lang }" :to="switchLang(code)">
            {{ code.toUpperCase() }}
          </NuxtLink>
        </div>
        <NuxtLink class="btn primary small" :to="`/${lang}/profile`">{{ t('nav.account') }}</NuxtLink>
      </div>
    </div>
  </header>
</template>
