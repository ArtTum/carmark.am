<script setup lang="ts">
const t = useT();
const lang = useLang();
const route = useRoute();

const form = reactive({
  q: String(route.query.q || ''),
  make: String(route.query.make || ''),
  model: String(route.query.model || ''),
  year_from: String(route.query.year_from || ''),
  year_to: String(route.query.year_to || ''),
  body: String(route.query.body || ''),
  auction: String(route.query.auction || ''),
  fuel: String(route.query.fuel || ''),
  transmission: String(route.query.transmission || ''),
  odometer_from: String(route.query.odometer_from || '0'),
  odometer_to: String(route.query.odometer_to || '250000'),
  sort: String(route.query.sort || ''),
  private_sale: String(route.query.private_sale || ''),
  buy_now: String(route.query.buy_now || ''),
});

const { data, pending } = await useFetch<any>(apiUrl('/vehicles'));

const fallbackVehicles = [
  {
    id: 'inv-1',
    slug: 'dodge-charger-gt-2022',
    title: { hy: '2022 Dodge Charger GT', en: '2022 Dodge Charger GT', ru: '2022 Dodge Charger GT' },
    images: ['/assets/extracted/04-214f9796b0b8.jpg', '/assets/extracted/18-a93576670a23.jpg'],
    lot: '12555884564',
    vin: '2C3CDXHG1NH145213',
    auction: 'Copart',
    sale_date: '2026-07-16 09:30:00',
    location: 'Los Angeles - CA',
    current_bid: 8700,
    buy_now: 25500,
    shipping_fee: 275,
    year: 2022,
    make: 'Dodge',
    model: 'Charger GT',
    body: 'Sedan',
    engine: '3.6 L',
    transmission: 'Automatic',
    fuel: 'Gasoline',
    color: 'White',
    condition: 'Front Damage',
    odometer: '30,226 mi',
  },
  {
    id: 'inv-2',
    slug: 'chevrolet-bolt-ev-2022',
    title: { hy: '2024 BMW M2', en: '2024 BMW M2', ru: '2024 BMW M2' },
    images: ['/assets/extracted/15-b5c50c63b3b9.jpg', '/assets/extracted/16-dcd4befb4127.png'],
    lot: '47746944',
    vin: 'WBA13DM07R8D33291',
    auction: 'IAAI',
    sale_date: '2026-07-17 11:00:00',
    location: 'Los Angeles - CA',
    current_bid: 5025,
    buy_now: 25500,
    shipping_fee: 275,
    year: 2024,
    make: 'BMW',
    model: 'M2',
    body: 'Coupe',
    engine: '3.0 L',
    transmission: 'Automatic',
    fuel: 'Gasoline',
    color: 'Red',
    condition: 'Normal Wear',
    odometer: '11,900 mi',
  },
  {
    id: 'inv-3',
    slug: 'ram-2500-tradesman-2018',
    title: { hy: '2021 Chevrolet Silverado 2500HD Heavy Duty', en: '2021 Chevrolet Silverado 2500HD Heavy Duty', ru: '2021 Chevrolet Silverado 2500HD Heavy Duty' },
    images: ['/assets/extracted/14-28b124d87f27.png'],
    lot: '12555884564',
    vin: '1GC4YLEY4MF137124',
    auction: 'Copart',
    sale_date: '2026-07-18 10:00:00',
    location: 'Ashland - KY',
    current_bid: 5025,
    buy_now: 25500,
    shipping_fee: 275,
    year: 2021,
    make: 'Chevrolet',
    model: 'Silverado',
    body: 'Pickup',
    engine: '6.6 L',
    transmission: 'Automatic',
    fuel: 'Diesel',
    color: 'White',
    condition: 'Run & Drive',
    odometer: '117,630 mi',
  },
  {
    id: 'inv-4',
    slug: 'toyota-sequoia-2001',
    title: { hy: '2021 Jeep Compass Trailhawk', en: '2021 Jeep Compass Trailhawk', ru: '2021 Jeep Compass Trailhawk' },
    images: ['/assets/extracted/03-17064740bf1e.jpg', '/assets/extracted/14-28b124d87f27.png'],
    lot: '12555884564',
    vin: '3C4NJDDB0MT508972',
    auction: 'IAAI',
    sale_date: '2026-07-19 10:00:00',
    location: 'Waldorf - MD',
    current_bid: 5025,
    buy_now: 25500,
    shipping_fee: 275,
    year: 2021,
    make: 'Jeep',
    model: 'Compass',
    body: 'SUV',
    engine: '2.4 L',
    transmission: 'Automatic',
    fuel: 'Gasoline',
    color: 'Gray',
    condition: 'Damaged',
    odometer: '86,000 mi',
  },
  {
    id: 'inv-5',
    slug: 'chevrolet-malibu-lt-2018',
    title: { hy: '2022 Toyota Tundra Platinum Tour', en: '2022 Toyota Tundra Platinum Tour', ru: '2022 Toyota Tundra Platinum Tour' },
    images: ['/assets/extracted/16-dcd4befb4127.png'],
    lot: '12555884564',
    vin: '5TFNA5DB0NX020114',
    auction: 'Copart',
    sale_date: '2026-07-20 12:00:00',
    location: 'Atlanta North - GA',
    current_bid: 5025,
    buy_now: 25500,
    shipping_fee: 275,
    year: 2022,
    make: 'Toyota',
    model: 'Tundra',
    body: 'Pickup',
    engine: '3.5 L',
    transmission: 'Automatic',
    fuel: 'Hybrid',
    color: 'Red',
    condition: 'Run & Drive',
    odometer: '30,226 mi',
  },
];

const sourceVehicles = computed(() => data.value?.data?.length ? data.value.data : fallbackVehicles);
const filters = computed(() => data.value?.filters || {});

const makeOptions = computed(() => Array.from(new Set([...(filters.value.makes || []), ...sourceVehicles.value.map((vehicle: any) => vehicle.make).filter(Boolean)])));
const modelOptions = computed(() => Array.from(new Set([...(filters.value.models || []), ...sourceVehicles.value.map((vehicle: any) => vehicle.model).filter(Boolean)])));
const bodyOptions = computed(() => Array.from(new Set([...(filters.value.bodies || []), ...sourceVehicles.value.map((vehicle: any) => vehicle.body).filter(Boolean)])));
const auctionOptions = computed(() => Array.from(new Set([...(filters.value.auctions || []), ...sourceVehicles.value.map((vehicle: any) => vehicle.auction).filter(Boolean)])));
const fuelOptions = computed(() => Array.from(new Set([...(filters.value.fuels || []), ...sourceVehicles.value.map((vehicle: any) => vehicle.fuel).filter(Boolean)])));

const activeFilters = computed(() => Object.entries(form).filter(([key, value]) => {
  if (!value || key === 'sort') return false;
  if (key === 'odometer_from') return value !== '0';
  if (key === 'odometer_to') return value !== '250000';
  return true;
}));
const currentPage = ref(Math.max(1, Number(route.query.page || 1) || 1));
const pageSize = 5;
const savedSearchNotice = ref('');
const inventoryTabs = [
  { key: 'all', label: 'Օգտագործված մեքենաներ', query: {} },
  { key: 'buy_now', label: 'Գնել հիմա', query: { buy_now: '1' } },
  { key: 'private', label: 'Չի պահանջում լիցենզիա', query: { private_sale: '1' } },
  { key: 'electric', label: 'Էլեկտրական մեքենաներ', query: { fuel: 'Electric' } },
  { key: 'hot', label: 'Թեժ առաջարկներ', query: { sort: 'price' } },
];
const yearFromOptions = ['', '2015', '2018', '2020', '2021', '2022', '2023', '2024'].map((year) => ({
  label: year || '2010',
  value: year,
}));
const yearToOptions = ['', '2018', '2020', '2021', '2022', '2023', '2024'].map((year) => ({
  label: year || '2020',
  value: year,
}));
type FacetKey = 'auction' | 'make' | 'model' | 'transmission' | 'body' | 'fuel';
type SectionKey = FacetKey | 'year' | 'odometer';

const odometerOptions = [
  { label: '0', value: '0' },
  { label: '25,000', value: '25000' },
  { label: '50,000', value: '50000' },
  { label: '100,000', value: '100000' },
  { label: '150,000', value: '150000' },
  { label: '250,000', value: '250000' },
];
const transmissionOptions = ['Ավտոմատ', 'Մեխանիկական', 'Էլեկտրական', 'Հիբրիդային'];
const transmissionMap: Record<string, string> = {
  Ավտոմատ: 'automatic',
  Մեխանիկական: 'manual',
  Էլեկտրական: 'electric',
  Հիբրիդային: 'hybrid',
};
const filterSearch = reactive<Record<FacetKey, string>>({
  auction: '',
  make: '',
  model: '',
  transmission: '',
  body: '',
  fuel: '',
});
const expandedFilters = reactive<Record<SectionKey, boolean>>({
  year: true,
  auction: true,
  make: true,
  model: true,
  transmission: true,
  body: true,
  odometer: true,
  fuel: true,
});
const showMoreFilters = reactive<Record<FacetKey, boolean>>({
  auction: false,
  make: false,
  model: false,
  transmission: false,
  body: false,
  fuel: false,
});
const sortOptions = [
  { label: 'Ընթացիկ հայտը', value: '' },
  { label: 'Ցածր գին', value: 'price' },
  { label: 'Նոր տարեթիվ', value: 'year' },
];
const visibleAuctionOptions = computed(() => visibleFacetOptions(auctionOptions.value, 'auction', 4));
const visibleMakeOptions = computed(() => visibleFacetOptions(makeOptions.value, 'make', 5));
const visibleModelOptions = computed(() => visibleFacetOptions(modelOptions.value, 'model', 5));
const visibleTransmissionOptions = computed(() => visibleFacetOptions(transmissionOptions, 'transmission', 4));
const visibleBodyOptions = computed(() => visibleFacetOptions(bodyOptions.value, 'body', 5));
const visibleFuelOptions = computed(() => visibleFacetOptions(fuelOptions.value, 'fuel', 5));
const facetHasMore = computed(() => ({
  auction: filteredFacetOptions(auctionOptions.value, 'auction').length > 4,
  make: filteredFacetOptions(makeOptions.value, 'make').length > 5,
  model: filteredFacetOptions(modelOptions.value, 'model').length > 5,
  transmission: filteredFacetOptions(transmissionOptions, 'transmission').length > 4,
  body: filteredFacetOptions(bodyOptions.value, 'body').length > 5,
  fuel: filteredFacetOptions(fuelOptions.value, 'fuel').length > 5,
}));

const filteredVehicles = computed(() => {
  const q = form.q.trim().toLowerCase();
  let list = sourceVehicles.value.filter((vehicle: any) => {
    const title = localize(vehicle.title).toLowerCase();
    const odometer = odometerNumber(vehicle.odometer);
    return (!q || [title, vehicle.make, vehicle.model, vehicle.lot].some((value) => String(value || '').toLowerCase().includes(q)))
      && (!form.make || vehicle.make === form.make)
      && (!form.model || vehicle.model === form.model)
      && (!form.body || vehicle.body === form.body)
      && (!form.auction || vehicle.auction === form.auction)
      && (!form.fuel || vehicle.fuel === form.fuel)
      && (!form.transmission || normalizeTransmission(vehicle.transmission) === normalizeTransmission(form.transmission))
      && (!form.private_sale || vehicle.private_sale)
      && (!form.buy_now || Number(vehicle.buy_now || 0) > 0)
      && (!form.year_from || Number(vehicle.year) >= Number(form.year_from))
      && (!form.year_to || Number(vehicle.year) <= Number(form.year_to))
      && (odometer === null || odometer >= Number(form.odometer_from || 0))
      && (odometer === null || odometer <= Number(form.odometer_to || 250000));
  });

  if (form.sort === 'price') list = [...list].sort((a: any, b: any) => Number(a.current_bid || 0) - Number(b.current_bid || 0));
  if (form.sort === 'year') list = [...list].sort((a: any, b: any) => Number(b.year || 0) - Number(a.year || 0));
  return list;
});

const totalPages = computed(() => Math.max(1, Math.ceil(filteredVehicles.value.length / pageSize)));
const paginatedVehicles = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredVehicles.value.slice(start, start + pageSize);
});
const visiblePages = computed(() => Array.from({ length: totalPages.value }, (_, index) => index + 1).slice(0, 10));
const activeTab = computed(() => {
  if (form.buy_now) return 'buy_now';
  if (form.private_sale) return 'private';
  if (form.fuel === 'Electric') return 'electric';
  if (form.sort === 'price') return 'hot';
  return 'all';
});

watch(() => route.query, (query) => {
  Object.assign(form, {
    q: String(query.q || ''),
    make: String(query.make || ''),
    model: String(query.model || ''),
    year_from: String(query.year_from || ''),
    year_to: String(query.year_to || ''),
    body: String(query.body || ''),
    auction: String(query.auction || ''),
    fuel: String(query.fuel || ''),
    transmission: String(query.transmission || ''),
    odometer_from: String(query.odometer_from || '0'),
    odometer_to: String(query.odometer_to || '250000'),
    sort: String(query.sort || ''),
    private_sale: String(query.private_sale || ''),
    buy_now: String(query.buy_now || ''),
  });
  currentPage.value = Math.max(1, Number(query.page || 1) || 1);
}, { deep: true });

watch(filteredVehicles, () => {
  if (currentPage.value > totalPages.value) currentPage.value = totalPages.value;
});

function cleanQuery() {
  return Object.fromEntries(Object.entries(form).filter(([key, value]) => {
    if (!value) return false;
    if (key === 'odometer_from') return value !== '0';
    if (key === 'odometer_to') return value !== '250000';
    return true;
  }));
}

function applyFilters() {
  currentPage.value = 1;
  return navigateTo({ path: `/${lang.value}/inventory`, query: cleanQuery() });
}

function applyTab(query: Record<string, string>) {
  Object.assign(form, {
    q: '',
    make: '',
    model: '',
    year_from: '',
    year_to: '',
    body: '',
    auction: '',
    fuel: '',
    transmission: '',
    odometer_from: '0',
    odometer_to: '250000',
    sort: '',
    private_sale: '',
    buy_now: '',
    ...query,
  });

  return applyFilters();
}

function clearFilter(key: string) {
  (form as any)[key] = '';
  return applyFilters();
}

function resetFilters() {
  Object.assign(form, {
    q: '',
    make: '',
    model: '',
    year_from: '',
    year_to: '',
    body: '',
    auction: '',
    fuel: '',
    transmission: '',
    odometer_from: '0',
    odometer_to: '250000',
    sort: '',
    private_sale: '',
    buy_now: '',
  });
  Object.keys(filterSearch).forEach((key) => {
    filterSearch[key as FacetKey] = '';
  });
  return applyFilters();
}

function setPage(page: number) {
  currentPage.value = Math.min(totalPages.value, Math.max(1, page));
  const query = cleanQuery();
  if (currentPage.value > 1) (query as any).page = String(currentPage.value);
  return navigateTo({ path: `/${lang.value}/inventory`, query });
}

function readStoredArray(key: string) {
  try {
    const value = JSON.parse(localStorage.getItem(key) || '[]');
    return Array.isArray(value) ? value : [];
  } catch {
    localStorage.removeItem(key);
    return [];
  }
}

function savedSearchLabel(query: Record<string, unknown>) {
  const entries = Object.entries(query).filter(([, value]) => value);
  if (!entries.length) return 'Բոլոր մեքենաները';

  const names: Record<string, string> = {
    q: 'Որոնում',
    make: 'Մակնիշ',
    model: 'Մոդել',
    year_from: 'Սկսած',
    year_to: 'Մինչև',
    body: 'Թափք',
    auction: 'Աճուրդ',
    fuel: 'Վառելիք',
    transmission: 'Փոխանցման տուփ',
    odometer_from: 'Օդոմետր սկսած',
    odometer_to: 'Օդոմետր մինչև',
    private_sale: 'Առանց լիցենզիայի',
    buy_now: 'Գնել հիմա',
  };

  return entries.map(([key, value]) => {
    if (key === 'buy_now' || key === 'private_sale') return names[key];
    return `${names[key] || key}: ${value}`;
  }).join(', ');
}

function saveSearch() {
  if (!import.meta.client) return;
  const query = cleanQuery();
  const signature = JSON.stringify(query);
  const searches = readStoredArray('savedSearches').filter((search: any) => JSON.stringify(search?.query || {}) !== signature);
  searches.unshift({
    id: Date.now(),
    query,
    label: savedSearchLabel(query),
    createdAt: new Date().toISOString(),
  });
  localStorage.setItem('savedSearches', JSON.stringify(searches.slice(0, 20)));
  savedSearchNotice.value = 'Որոնումը պահպանվեց';
  window.setTimeout(() => {
    savedSearchNotice.value = '';
  }, 2400);
}

function filteredFacetOptions(options: unknown[], key: FacetKey) {
  const query = filterSearch[key].trim().toLowerCase();
  const normalized = Array.from(new Set(options.map((option) => String(option || '')).filter(Boolean)));
  if (!query) return normalized;
  return normalized.filter((option) => option.toLowerCase().includes(query));
}

function visibleFacetOptions(options: unknown[], key: FacetKey, limit = 5) {
  const filtered = filteredFacetOptions(options, key);
  return showMoreFilters[key] ? filtered : filtered.slice(0, limit);
}

function toggleSection(key: SectionKey) {
  expandedFilters[key] = !expandedFilters[key];
}

function isFacetChecked(key: FacetKey, value: string) {
  return String((form as any)[key] || '') === value;
}

function toggleFacet(key: FacetKey, value: string) {
  (form as any)[key] = String((form as any)[key] || '') === value ? '' : value;
  return applyFilters();
}

function normalizeTransmission(value: unknown) {
  const raw = String(value || '').trim();
  return (transmissionMap[raw] || raw).toLowerCase();
}

function odometerNumber(value: unknown) {
  const digits = String(value || '').replace(/[^\d]/g, '');
  return digits ? Number(digits) : null;
}
</script>

<template>
  <main class="inventory-page">
    <section class="inventory-shell section">
      <div class="container">
        <h1>{{ t('inventory.title') }}</h1>

        <div class="inventory-tabs">
          <button
            v-for="tab in inventoryTabs"
            :key="tab.key"
            type="button"
            :class="{ active: activeTab === tab.key }"
            @click="applyTab(tab.query)"
          >
            {{ tab.label }}
          </button>
        </div>

        <div class="inventory-layout">
          <aside class="filter-sidebar compact-filter-sidebar">
            <div class="filter-title">
              <h2>{{ t('inventory.filters') }}</h2>
              <button type="button" @click="resetFilters">Ջնջել</button>
            </div>

            <form class="facet-filter-form" @submit.prevent="applyFilters">
              <section class="facet-section">
                <button class="facet-toggle" type="button" @click="toggleSection('year')">
                  <span>Տարի</span>
                  <svg :class="{ open: expandedFilters.year }" width="10" height="6" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                    <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
                <div v-if="expandedFilters.year" class="facet-range-row">
                  <AppSelect v-model="form.year_from" :options="yearFromOptions" @change="applyFilters" />
                  <AppSelect v-model="form.year_to" :options="yearToOptions" @change="applyFilters" />
                </div>
              </section>

              <section class="facet-section">
                <button class="facet-toggle" type="button" @click="toggleSection('auction')">
                  <span>Ընկերություն</span>
                  <svg :class="{ open: expandedFilters.auction }" width="10" height="6" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                    <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
                <div v-if="expandedFilters.auction" class="facet-options">
                  <label v-for="auction in visibleAuctionOptions" :key="auction" class="facet-check">
                    <input type="checkbox" :checked="isFacetChecked('auction', auction)" @change="toggleFacet('auction', auction)">
                    <span>{{ auction }}</span>
                  </label>
                </div>
              </section>

              <section class="facet-section">
                <button class="facet-toggle" type="button" @click="toggleSection('make')">
                  <span>Մակնիշ</span>
                  <svg :class="{ open: expandedFilters.make }" width="10" height="6" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                    <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
                <div v-if="expandedFilters.make" class="facet-body">
                  <label class="facet-search">
                    <input v-model="filterSearch.make" type="search" placeholder="Որոնել">
                  </label>
                  <div class="facet-options">
                    <label v-for="make in visibleMakeOptions" :key="make" class="facet-check">
                      <input type="checkbox" :checked="isFacetChecked('make', make)" @change="toggleFacet('make', make)">
                      <span>{{ make }}</span>
                    </label>
                  </div>
                  <button v-if="facetHasMore.make" class="facet-more" type="button" @click="showMoreFilters.make = !showMoreFilters.make">
                    {{ showMoreFilters.make ? 'Փակել' : 'Տեսնել ավելին' }}
                  </button>
                </div>
              </section>

              <section class="facet-section">
                <button class="facet-toggle" type="button" @click="toggleSection('model')">
                  <span>Մոդել</span>
                  <svg :class="{ open: expandedFilters.model }" width="10" height="6" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                    <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
                <div v-if="expandedFilters.model" class="facet-body">
                  <label class="facet-search">
                    <input v-model="filterSearch.model" type="search" placeholder="Որոնել">
                  </label>
                  <div class="facet-options">
                    <label v-for="model in visibleModelOptions" :key="model" class="facet-check">
                      <input type="checkbox" :checked="isFacetChecked('model', model)" @change="toggleFacet('model', model)">
                      <span>{{ model }}</span>
                    </label>
                  </div>
                  <button v-if="facetHasMore.model" class="facet-more" type="button" @click="showMoreFilters.model = !showMoreFilters.model">
                    {{ showMoreFilters.model ? 'Փակել' : 'Տեսնել ավելին' }}
                  </button>
                </div>
              </section>

              <section class="facet-section">
                <button class="facet-toggle" type="button" @click="toggleSection('transmission')">
                  <span>Փոխանցման տուփ</span>
                  <svg :class="{ open: expandedFilters.transmission }" width="10" height="6" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                    <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
                <div v-if="expandedFilters.transmission" class="facet-body">
                  <label class="facet-search">
                    <input v-model="filterSearch.transmission" type="search" placeholder="Որոնել">
                  </label>
                  <div class="facet-options">
                    <label v-for="item in visibleTransmissionOptions" :key="item" class="facet-check">
                      <input type="checkbox" :checked="isFacetChecked('transmission', item)" @change="toggleFacet('transmission', item)">
                      <span>{{ item }}</span>
                    </label>
                  </div>
                </div>
              </section>

              <section class="facet-section">
                <button class="facet-toggle" type="button" @click="toggleSection('body')">
                  <span>Թափք</span>
                  <svg :class="{ open: expandedFilters.body }" width="10" height="6" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                    <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
                <div v-if="expandedFilters.body" class="facet-body">
                  <div class="facet-options">
                    <label v-for="body in visibleBodyOptions" :key="body" class="facet-check">
                      <input type="checkbox" :checked="isFacetChecked('body', body)" @change="toggleFacet('body', body)">
                      <span>{{ body }}</span>
                    </label>
                  </div>
                  <button v-if="facetHasMore.body" class="facet-more" type="button" @click="showMoreFilters.body = !showMoreFilters.body">
                    {{ showMoreFilters.body ? 'Փակել' : 'Տեսնել ավելին' }}
                  </button>
                </div>
              </section>

              <section class="facet-section">
                <button class="facet-toggle" type="button" @click="toggleSection('odometer')">
                  <span>Օդոմետր</span>
                  <svg :class="{ open: expandedFilters.odometer }" width="10" height="6" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                    <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
                <div v-if="expandedFilters.odometer" class="facet-range-row">
                  <AppSelect v-model="form.odometer_from" :options="odometerOptions" @change="applyFilters" />
                  <AppSelect v-model="form.odometer_to" :options="odometerOptions" @change="applyFilters" />
                </div>
              </section>

              <section class="facet-section">
                <button class="facet-toggle" type="button" @click="toggleSection('fuel')">
                  <span>Վառելիքի տեսակ</span>
                  <svg :class="{ open: expandedFilters.fuel }" width="10" height="6" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                    <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
                <div v-if="expandedFilters.fuel" class="facet-options">
                  <label v-for="fuel in visibleFuelOptions" :key="fuel" class="facet-check">
                    <input type="checkbox" :checked="isFacetChecked('fuel', fuel)" @change="toggleFacet('fuel', fuel)">
                    <span>{{ fuel }}</span>
                  </label>
                </div>
              </section>
            </form>
          </aside>

          <div class="inventory-results">
            <div class="inventory-toolbar">
              <div class="inventory-toolbar-main">
                <div class="inventory-result-count">
                  <strong>{{ filteredVehicles.length.toLocaleString('en-US') }}</strong>
                  <span>{{ t('inventory.results') }}</span>
                </div>
                <button class="save-search-btn" type="button" @click="saveSearch">
                  <svg width="21" height="19" viewBox="0 0 23 20" fill="none" aria-hidden="true">
                    <path d="M6.31055 0.800781H6.3291C8.08439 0.791775 9.75218 1.51194 10.9121 2.7666L11.5 3.40137L12.0879 2.7666C13.2478 1.51194 14.9156 0.791775 16.6709 0.800781H16.6895C18.1503 0.774683 19.5554 1.31766 20.5898 2.29883C21.6234 3.27921 22.2004 4.61427 22.2002 6.00098C22.2001 8.64038 20.5684 11.053 18.251 13.3281C17.1044 14.4537 15.8236 15.5136 14.5557 16.5234C13.5011 17.3634 12.4401 18.1829 11.502 18.957C10.5642 18.1764 9.5048 17.3536 8.44922 16.5117C7.18087 15.5001 5.89946 14.4413 4.75195 13.3174C2.4331 11.0462 0.799909 8.64179 0.799805 6.00098C0.799557 4.61431 1.37661 3.27922 2.41016 2.29883C3.44464 1.31765 4.84971 0.774684 6.31055 0.800781Z" stroke="currentColor" stroke-width="1.6" />
                  </svg>
                  <span>Պահպանել որոնումը</span>
                </button>
              </div>
              <label class="inventory-sort-control">
                <span>Դասավորել ըստ</span>
                <AppSelect v-model="form.sort" class="toolbar-select" :options="sortOptions" @change="applyFilters" />
              </label>
            </div>

            <div v-if="activeFilters.length" class="active-filter-row">
              <button v-for="[key, value] in activeFilters" :key="key" type="button" @click="clearFilter(key)">
                <span>{{ value }}</span>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                  <path d="M9 3L3 9M3 3L9 9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                </svg>
              </button>
            </div>
            <p v-if="savedSearchNotice" class="success-note">{{ savedSearchNotice }}</p>

            <div v-if="pending" class="loading-state">
              <span />
              <p>Տվյալները բեռնվում են...</p>
            </div>

            <div v-if="paginatedVehicles.length" class="inventory-list">
              <VehicleCard v-for="vehicle in paginatedVehicles" :key="vehicle.id" :vehicle="vehicle" wide />
            </div>
            <div v-else class="empty-state">
              <h2>Մեքենա չի գտնվել</h2>
              <p class="muted">Փորձեք փոխել ֆիլտրերը կամ ջնջել որոնման պայմանները։</p>
              <button class="btn ghost" type="button" @click="resetFilters">Ջնջել ֆիլտրերը</button>
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
        </div>
      </div>
    </section>
  </main>
</template>
