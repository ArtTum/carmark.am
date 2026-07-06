<script setup lang="ts">
const t = useT();
const lang = useLang();
const { data } = await useFetch<any>(apiUrl('/site'));
const settings = computed(() => data.value?.settings || {});
</script>

<template>
  <footer class="site-footer">
    <div class="container footer-grid">
      <div>
        <Logo :to="`/${lang}`" />
        <p>{{ localize(settings.address || {}) }}</p>
        <p>{{ settings.email }}</p>
        <p>{{ settings.phone }}</p>
      </div>
      <nav>
        <h3>{{ t('nav.buy') }}</h3>
        <NuxtLink :to="`/${lang}/inventory`">{{ t('inventory.title') }}</NuxtLink>
        <NuxtLink :to="`/${lang}/auctions`">{{ t('nav.auctions') }}</NuxtLink>
        <NuxtLink :to="`/${lang}/how-to-buy`">{{ t('nav.how') }}</NuxtLink>
      </nav>
      <nav>
        <h3>{{ t('nav.services') }}</h3>
        <NuxtLink :to="`/${lang}/services`">{{ t('nav.services') }}</NuxtLink>
        <NuxtLink :to="`/${lang}/about`">About</NuxtLink>
        <NuxtLink :to="`/${lang}/calculator`">Calculator</NuxtLink>
      </nav>
      <nav>
        <h3>{{ t('nav.contact') }}</h3>
        <NuxtLink :to="`/${lang}/contact`">{{ t('nav.contact') }}</NuxtLink>
        <a href="#">Facebook</a>
        <a href="#">Instagram</a>
      </nav>
    </div>
    <div class="container footer-bottom">
      <span>&copy;2026 CarMark</span>
      <span>{{ t('footer.rights') }}</span>
    </div>
  </footer>
</template>
