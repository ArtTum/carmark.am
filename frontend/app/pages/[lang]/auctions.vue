<script setup lang="ts">
const lang = useLang();
const t = useT();
const route = useRoute();
const activeTab = ref(String(route.query.tab || 'current') === 'upcoming' ? 'upcoming' : 'current');
const currentPage = ref(1);
const pageSize = 5;
const { data } = await useFetch<any>(apiUrl('/auctions'));

const fallbackAuctions = Array.from({ length: 8 }, (_, index) => ({
  id: `auction-${index}`,
  name: index % 2 ? 'Los Angeles' : 'San Diego',
  address: index % 2 ? 'CA - Los Angeles' : 'CA - San Diego',
  starts_at: index < 4 ? `2026-07-${14 + index} 12:30:00` : `2026-07-${22 + index} 11:00:00`,
  lots_count: index === 0 ? 5400 : 216,
  line: index % 2 ? 'Line B' : 'Line A',
}));

const auctions = computed(() => data.value?.data?.length ? data.value.data : fallbackAuctions);
const filteredAuctions = computed(() => {
  const now = new Date();
  return auctions.value.filter((auction: any, index: number) => {
    const starts = new Date(auction.starts_at);
    const diffDays = (starts.getTime() - now.getTime()) / 86400000;
    if (activeTab.value === 'current') return diffDays <= 14 || index < 4;
    return diffDays > 14 || index >= 4;
  });
});
const totalPages = computed(() => Math.max(1, Math.ceil(filteredAuctions.value.length / pageSize)));
const paginatedAuctions = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredAuctions.value.slice(start, start + pageSize);
});
const visiblePages = computed(() => Array.from({ length: totalPages.value }, (_, index) => index + 1));

watch(activeTab, () => {
  currentPage.value = 1;
});

watch(() => route.query.tab, (tab) => {
  activeTab.value = String(tab || 'current') === 'upcoming' ? 'upcoming' : 'current';
});

function formatDate(value: string) {
  const date = new Date(value);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  return `${day}/${month}/${date.getFullYear()}`;
}

function setPage(page: number) {
  currentPage.value = Math.min(totalPages.value, Math.max(1, page));
}
</script>

<template>
  <main class="auction-page">
    <section class="section auction-shell">
      <div class="container">
        <h1>{{ t('nav.auctions') }}</h1>
        <div class="auction-tabs">
          <button type="button" :class="{ active: activeTab === 'current' }" @click="activeTab = 'current'">Ընթացիկ</button>
          <button type="button" :class="{ active: activeTab === 'upcoming' }" @click="activeTab = 'upcoming'">Սպասվող</button>
        </div>

        <div class="auction-list-table">
          <article v-for="auction in paginatedAuctions" :key="auction.id" class="auction-row-card">
            <strong>{{ formatDate(auction.starts_at) }}</strong>
            <span>
              <b>{{ auction.name }}</b>
              <small>{{ auction.address }}</small>
            </span>
            <span>
              <b>Ավարտվում է <em>2ժ · 30ր · 20վ</em></b>
              <small>12:30 PM UTC-05:00</small>
            </span>
            <span><b>{{ Number(auction.lots_count).toLocaleString('en-US') }} մեքենա</b></span>
            <span><b>{{ auction.line || 'Line A' }}</b></span>
            <NuxtLink class="btn primary" :to="{ path: `/${lang}/inventory`, query: { auction: auction.name } }">{{ t('btn.details') }}</NuxtLink>
          </article>
        </div>
        <div v-if="!paginatedAuctions.length" class="empty-state">
          <h2>Աճուրդ չի գտնվել</h2>
          <p class="muted">Ընտրեք այլ բաժին կամ փորձեք ավելի ուշ։</p>
        </div>

        <div v-if="totalPages > 1" class="pagination-row">
          <button type="button" :disabled="currentPage === 1" @click="setPage(currentPage - 1)">Նախորդը</button>
          <button
            v-for="page in visiblePages"
            :key="page"
            type="button"
            :class="{ current: currentPage === page }"
            @click="setPage(page)"
          >
            {{ page }}
          </button>
          <button type="button" :disabled="currentPage === totalPages" @click="setPage(currentPage + 1)">Հաջորդը</button>
        </div>
      </div>
    </section>
  </main>
</template>
