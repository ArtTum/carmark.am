<script setup lang="ts">
const lang = useLang();
const t = useT();
const heroSearchRef = ref<HTMLElement | null>(null);
const siteSettings = useSiteSettings();

const search = reactive({
  make: '',
  model: '',
  year_from: '1960',
  year_to: '2024',
});

const { data: vehicleData } = await useFetch<any>(apiUrl('/vehicles'));
const vehicles = computed(() => vehicleData.value?.data || []);

const defaultMakes = ['Ford', 'Nissan', 'Honda', 'Dodge', 'Mercedes-Benz', 'Toyota', 'Mazda', 'Audi'];
const defaultModelsByMake: Record<string, string[]> = {
  Audi: ['A4', 'A6', 'Q5'],
  BMW: ['3 Series', '5 Series', 'X3', 'X5'],
  Chevrolet: ['Bolt EV', 'Malibu', 'Silverado'],
  Dodge: ['Charger', 'Durango'],
  Ford: ['Fusion', 'Escape', 'Explorer', 'F-150'],
  Honda: ['Civic', 'Accord', 'CR-V'],
  Hyundai: ['Elantra', 'Sonata', 'Tucson'],
  Mazda: ['3', '6', 'CX-5'],
  'Mercedes-Benz': ['C-Class', 'E-Class', 'GLC'],
  Nissan: ['Altima', 'Rogue', 'Sentra'],
  Toyota: ['Camry', 'Prius', 'Sequoia', 'Tundra'],
};

const fallbackVehicles = [
  {
    id: 'home-kia',
    slug: 'toyota-sequoia-2001',
    title: { hy: '2017 Kia Optima LX', en: '2017 Kia Optima LX', ru: '2017 Kia Optima LX' },
    images: ['/assets/home/showcase/2017-kia-optima-lx.jpg', '/assets/home/showcase/2023-toyota-prius.jpg'],
    lot: '22566654523124',
    auction: 'IAAI',
    sale_date: '2026-07-15 07:00:00',
    location: 'Los Angeles - CA',
    current_bid: 5025,
    buy_now: 25500,
    shipping_fee: 275,
    year: 2017,
    body: 'Sedan',
    fuel: 'Gasoline',
  },
  {
    id: 'home-bolt',
    slug: 'chevrolet-bolt-ev-2022',
    title: { hy: '2022 Chevrolet Bolt EV FWD 2LT', en: '2022 Chevrolet Bolt EV FWD 2LT', ru: '2022 Chevrolet Bolt EV FWD 2LT' },
    images: ['/assets/home/showcase/2022-chevrolet-bolt-ev.jpg', '/assets/home/showcase/2018-chevrolet-malibu-lt.png'],
    lot: '22566654523124',
    auction: 'IAAI',
    sale_date: '2026-07-15 07:00:00',
    location: 'Los Angeles - CA',
    current_bid: 5025,
    buy_now: 22000,
    shipping_fee: 275,
    year: 2022,
    body: 'Hatchback',
    fuel: 'Electric',
  },
  {
    id: 'home-malibu',
    slug: 'chevrolet-malibu-lt-2018',
    title: { hy: '2018 Chevrolet Malibu LT', en: '2018 Chevrolet Malibu LT', ru: '2018 Chevrolet Malibu LT' },
    images: ['/assets/home/showcase/2018-chevrolet-malibu-lt.png', '/assets/home/showcase/2023-hyundai-sonata-sel.png'],
    lot: '22566654523124',
    auction: 'Copart',
    sale_date: '2026-07-15 07:00:00',
    location: 'Los Angeles - CA',
    current_bid: 5025,
    buy_now: 20500,
    shipping_fee: 275,
    year: 2018,
    body: 'Sedan',
    fuel: 'Gasoline',
  },
  {
    id: 'home-sonata',
    slug: 'hyundai-sonata-sel-2023',
    title: { hy: '2023 Hyundai Sonata SEL', en: '2023 Hyundai Sonata SEL', ru: '2023 Hyundai Sonata SEL' },
    images: ['/assets/home/showcase/2023-hyundai-sonata-sel.png'],
    lot: '22566654523124',
    auction: 'IAAI',
    sale_date: '2026-07-15 07:00:00',
    location: 'Ավտոսրահ, 11,900կմ',
    current_bid: 25000,
    buy_now: 32000,
    shipping_fee: 275,
    year: 2023,
    body: 'Sedan',
    fuel: 'Gasoline',
  },
  {
    id: 'home-ram',
    slug: 'ram-2500-tradesman-2018',
    title: { hy: '2018 Ram 2500 Tradesman 4x4', en: '2018 Ram 2500 Tradesman 4x4', ru: '2018 Ram 2500 Tradesman 4x4' },
    images: ['/assets/home/showcase/2018-ram-2500-tradesman.png'],
    lot: '22566654523124',
    auction: 'Copart',
    sale_date: '2026-07-15 07:00:00',
    location: 'Ավտոսրահ, 11,900կմ',
    current_bid: 25000,
    buy_now: 34800,
    shipping_fee: 275,
    year: 2018,
    body: 'Pickup',
    fuel: 'Gasoline',
  },
  {
    id: 'home-silverado',
    slug: 'dodge-charger-gt-2022',
    title: { hy: '2021 Chevrolet Silverado 1500', en: '2021 Chevrolet Silverado 1500', ru: '2021 Chevrolet Silverado 1500' },
    images: ['/assets/home/showcase/2021-chevrolet-silverado.jpg'],
    lot: '22566654523124',
    auction: 'Copart',
    sale_date: '2026-07-15 07:00:00',
    location: 'Ավտոսրահ, 11,900կմ',
    current_bid: 25000,
    buy_now: 39000,
    shipping_fee: 275,
    year: 2021,
    body: 'Pickup',
    fuel: 'Gasoline',
  },
  {
    id: 'home-prius',
    slug: 'chevrolet-bolt-ev-2022',
    title: { hy: '2023 Toyota Prius XLE AWD-E', en: '2023 Toyota Prius XLE AWD-E', ru: '2023 Toyota Prius XLE AWD-E' },
    images: ['/assets/home/showcase/2023-toyota-prius.jpg'],
    lot: '22566654523124',
    auction: 'IAAI',
    sale_date: '2026-07-15 07:00:00',
    location: 'Ավտոսրահ, 11,900կմ',
    current_bid: 25000,
    buy_now: 31000,
    shipping_fee: 275,
    year: 2023,
    body: 'Hatchback',
    fuel: 'Hybrid',
  },
];

function vehicleKey(vehicle: any) {
  return String(vehicle?.id ?? vehicle?.slug ?? '');
}

function fillVehicleRow(list: any[], source: any[], count: number) {
  const result = [...list];
  const used = new Set(result.map(vehicleKey).filter(Boolean));

  for (const vehicle of source) {
    if (result.length >= count) break;

    const key = vehicleKey(vehicle);
    if (key && used.has(key)) continue;

    result.push(vehicle);
    if (key) used.add(key);
  }

  return result.slice(0, count);
}

const vehiclePool = computed(() => vehicles.value.length >= 6 ? vehicles.value : fallbackVehicles);
const featuredVehicles = computed(() => vehiclePool.value.slice(0, 3));
const privateVehicles = computed(() => fillVehicleRow(vehiclePool.value.slice(3, 7), vehiclePool.value, 4));
const makeOptions = computed(() => Array.from(new Set([...defaultMakes, ...vehicles.value.map((vehicle: any) => vehicle.make).filter(Boolean)])));
const modelOptions = computed(() => {
  if (!search.make) return Array.from(new Set(Object.values(defaultModelsByMake).flat()));
  return defaultModelsByMake[search.make] || [];
});
const defaultYearFrom = '1960';
const defaultYearTo = '2024';
const yearFromOptions = ['1960', '1970', '1980', '1990', '2000', '2010', '2018', '2020'];
const yearToOptions = ['2024', '2023', '2022', '2021', '2020', '2019', '2018', '2015'];
type SearchFilter = 'make' | 'model' | 'year_from' | 'year_to';
const openFilter = ref<SearchFilter | null>(null);
const filterQuery = ref('');
const auctionClock = ref(new Date());
let auctionTimer: ReturnType<typeof setInterval> | undefined;

const filteredMakes = computed(() => filterByQuery(makeOptions.value));
const filteredModels = computed(() => filterByQuery(modelOptions.value));

const { data: auctionData } = await useFetch<any>(apiUrl('/auctions'));

const fallbackAuctionCards = [
  {
    id: 'ashland',
    name: 'Ashland (KY)',
    address: '123 Four Wheel Dr, Ashland, KY 41102',
    starts_at: '2026-07-09 10:00:00',
    lots_count: 556,
    image: '/assets/home/showcase/auction-ashland.png',
  },
  {
    id: 'los-angeles',
    name: 'Public car auction in Los Angeles - CA',
    address: '18300 S. Vermont Ave, Gardena, CA 90248',
    starts_at: '2026-07-10 11:30:00',
    lots_count: 556,
    image: '/assets/home/showcase/auction-los-angeles.png',
  },
  {
    id: 'atlanta',
    name: 'Atlanta North (GA)',
    address: '6242 Blackacre Trl. N.W, Acworth, GA 30101',
    starts_at: '2026-07-11 10:00:00',
    lots_count: 556,
    image: '/assets/home/showcase/auction-atlanta-north.png',
  },
];

const knownAuctionImages = [
  { match: 'ashland', image: '/assets/home/showcase/auction-ashland.png' },
  { match: 'los angeles', image: '/assets/home/showcase/auction-los-angeles.png' },
  { match: 'atlanta', image: '/assets/home/showcase/auction-atlanta-north.png' },
];

const brandLogos: Record<string, string> = {
  Ford: '/assets/extracted/10-c70ec2f2fc18.png',
  Nissan: '/assets/extracted/11-8908995e8e02.png',
  Honda: '/assets/extracted/12-763c3cf21bed.png',
  Dodge: '/assets/home/brands/dodge.svg',
  'Mercedes-Benz': '/assets/home/brands/mercedes-benz.svg',
  Toyota: '/assets/home/brands/toyota.svg',
  Mazda: '/assets/home/brands/mazda.svg',
  Audi: '/assets/home/brands/audi.svg',
};

const brandSlide = ref(0);
const maxBrandSlide = computed(() => Math.max(0, makeOptions.value.length - 8));
const brandTrackStyle = computed(() => ({ '--brand-slide': String(brandSlide.value) }));

const serviceCards = [
  {
    title: 'Դեպոզիտի վճարում',
    text: 'Նախնական աճուրդային գնի 15%-ի չափով, բայց ոչ պակաս քան 500,000 դրամ։',
    icon: '/assets/home/services/deposit.svg',
  },
  {
    title: 'Մեքենայի պատմություն',
    text: 'Աճուրդին մասնակցելուց առաջ ստուգում ենք մեքենայի պատմությունը, վնասները և փաստաթղթերը։',
    icon: '/assets/home/services/history.svg',
  },
  {
    title: 'Աճուրդի մասնակցություն',
    text: 'Մասնակցեք live աճուրդին մեր թիմի ուղեկցությամբ և վերահսկեք վերջնական գինը։',
    icon: '/assets/home/services/auction.svg',
  },
  {
    title: 'Պայմանագրի կնքում',
    text: 'Գնումը ամրագրվում է պայմանագրով՝ հստակ ժամկետներով ու վճարման փուլերով։',
    icon: '/assets/home/services/contract.svg',
  },
];

const homeContent = usePageContent('home', {
  hero: {
    background_image: '/assets/home/bmw-vision-m-next-be.png',
  },
  section_titles: {},
  brand_logos: {},
  auction_cards: [],
  services: serviceCards,
});
const sectionTitles = computed(() => homeContent.value.section_titles || {});
const renderedAuctionCards = computed(() => (
  homeContent.value.auction_cards?.length
    ? homeContent.value.auction_cards
    : (auctionData.value?.data?.length ? auctionData.value.data : fallbackAuctionCards)
));
const renderedServiceCards = computed(() => homeContent.value.services?.length ? homeContent.value.services : serviceCards);
const renderedBrandLogos = computed(() => ({ ...brandLogos, ...(siteSettings.value.brand_logos || {}), ...(homeContent.value.brand_logos || {}) }));
const heroStyle = computed(() => {
  const image = homeContent.value.hero?.background_image;
  return image ? { '--hero-bg': `url('${image}')` } : {};
});

function filterByQuery(options: string[]) {
  const query = filterQuery.value.trim().toLowerCase();
  if (!query) return options;
  return options.filter((option) => option.toLowerCase().includes(query));
}

function toggleFilter(filter: SearchFilter) {
  openFilter.value = openFilter.value === filter ? null : filter;
  filterQuery.value = '';
}

function closeFilter() {
  openFilter.value = null;
  filterQuery.value = '';
}

function handleHomePointerdown(event: PointerEvent) {
  if (openFilter.value && !heroSearchRef.value?.contains(event.target as Node)) closeFilter();
}

function handleHomeKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape') closeFilter();
}

function setFilter(filter: SearchFilter, value: string) {
  search[filter] = value;

  if (filter === 'make') {
    search.model = '';
  }

  closeFilter();
}

function filterLabel(filter: SearchFilter) {
  const labels: Record<SearchFilter, string> = {
    make: 'Բոլոր Մակնիշները',
    model: 'Բոլոր Մոդելները',
    year_from: defaultYearFrom,
    year_to: defaultYearTo,
  };

  return search[filter] || labels[filter];
}

function isSelected(filter: SearchFilter) {
  if (filter === 'year_from') return Boolean(search.year_from && search.year_from !== defaultYearFrom);
  if (filter === 'year_to') return Boolean(search.year_to && search.year_to !== defaultYearTo);
  return Boolean(search[filter]);
}

function submitSearch() {
  const query = Object.fromEntries(Object.entries(search).filter(([, value]) => value));
  closeFilter();
  return navigateTo({ path: `/${lang.value}/inventory`, query });
}

function goBrand(make: string) {
  return navigateTo({ path: `/${lang.value}/inventory`, query: { make } });
}

function moveBrandSlider(direction: number) {
  brandSlide.value = Math.min(maxBrandSlide.value, Math.max(0, brandSlide.value + direction));
}

function auctionDate(value: string) {
  const date = new Date(value);
  const monthNames: Record<string, string[]> = {
    hy: ['հնվ', 'փտր', 'մրտ', 'ապր', 'մյս', 'հնս', 'հլս', 'օգս', 'սեպ', 'հոկ', 'նոյ', 'դեկ'],
    en: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    ru: ['янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'],
  };
  const months = monthNames[lang.value] || monthNames.en;
  return `${months[date.getMonth()]} ${date.getDate()}`;
}

function auctionCountdown(value: string) {
  const target = new Date(value).getTime();
  if (!Number.isFinite(target)) return '0օր, 0ժ, 00ր';

  const diff = Math.max(0, target - auctionClock.value.getTime());
  const days = Math.floor(diff / 86_400_000);
  const hours = Math.floor((diff % 86_400_000) / 3_600_000);
  const minutes = Math.floor((diff % 3_600_000) / 60_000);

  return `${days}օր, ${hours}ժ, ${String(minutes).padStart(2, '0')}ր`;
}

function auctionImage(auction: any) {
  const image = String(auction?.image || '');
  const name = String(auction?.name || '').toLowerCase();
  const known = knownAuctionImages.find((item) => name.includes(item.match));

  if ((!image || image.includes('/assets/extracted/')) && known) return known.image;
  return image || known?.image || '/assets/home/showcase/auction-ashland.png';
}

onMounted(() => {
  auctionClock.value = new Date();
  auctionTimer = setInterval(() => {
    auctionClock.value = new Date();
  }, 60_000);
  document.addEventListener('pointerdown', handleHomePointerdown);
  document.addEventListener('keydown', handleHomeKeydown);
});

onBeforeUnmount(() => {
  if (auctionTimer) clearInterval(auctionTimer);
  document.removeEventListener('pointerdown', handleHomePointerdown);
  document.removeEventListener('keydown', handleHomeKeydown);
});
</script>

<template>
  <main class="home-page">
    <section class="home-hero" :style="heroStyle">
      <div class="container hero-shell">
        <h1>{{ localize(homeContent.hero?.title) || t('hero.title') }}</h1>
        <p>{{ localize(homeContent.hero?.copy) || t('hero.copy') }}</p>

        <form ref="heroSearchRef" class="hero-search-card" @submit.prevent="submitSearch">
          <div class="search-tabs">
            <button class="active" type="button">{{ t('nav.buy') }}</button>
            <button type="button" @click="navigateTo(`/${lang}/auctions`)">{{ t('nav.auctions') }}</button>
          </div>
          <div class="hero-search-grid">
            <label :class="{ 'filter-active': openFilter === 'make' }">
              <span class="filter-label">Մակնիշ</span>
              <button class="select-trigger" type="button" :class="{ selected: isSelected('make'), open: openFilter === 'make' }" :aria-expanded="openFilter === 'make'" @click="toggleFilter('make')">
                <span class="select-value">{{ filterLabel('make') }}</span>
                <svg class="select-chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                  <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>
              <div v-if="openFilter === 'make'" class="filter-popover">
                <div class="filter-search-row">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10.8 18.1a7.3 7.3 0 1 1 0-14.6 7.3 7.3 0 0 1 0 14.6Z" />
                    <path d="m16.3 16.3 4.2 4.2" />
                  </svg>
                  <input v-model="filterQuery" type="search" placeholder="Որոնել">
                </div>
                <button class="filter-option" type="button" :class="{ active: !search.make }" @click="setFilter('make', '')">
                  <span>Բոլոր Մակնիշները</span>
                  <span class="filter-check" />
                </button>
                <button v-for="make in filteredMakes" :key="make" class="filter-option" type="button" :class="{ active: search.make === make }" @click="setFilter('make', make)">
                  <span>{{ make }}</span>
                  <span class="filter-check" />
                </button>
              </div>
            </label>
            <label :class="{ 'filter-active': openFilter === 'model' }">
              <span class="filter-label">Մոդելներ</span>
              <button class="select-trigger" type="button" :class="{ selected: isSelected('model'), open: openFilter === 'model' }" :aria-expanded="openFilter === 'model'" @click="toggleFilter('model')">
                <span class="select-value">{{ filterLabel('model') }}</span>
                <svg class="select-chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                  <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>
              <div v-if="openFilter === 'model'" class="filter-popover">
                <div class="filter-search-row">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10.8 18.1a7.3 7.3 0 1 1 0-14.6 7.3 7.3 0 0 1 0 14.6Z" />
                    <path d="m16.3 16.3 4.2 4.2" />
                  </svg>
                  <input v-model="filterQuery" type="search" placeholder="Որոնել">
                </div>
                <button class="filter-option" type="button" :class="{ active: !search.model }" @click="setFilter('model', '')">
                  <span>Բոլոր Մոդելները</span>
                  <span class="filter-check" />
                </button>
                <button v-for="model in filteredModels" :key="model" class="filter-option" type="button" :class="{ active: search.model === model }" @click="setFilter('model', model)">
                  <span>{{ model }}</span>
                  <span class="filter-check" />
                </button>
              </div>
            </label>
            <label :class="{ 'filter-active': openFilter === 'year_from' }">
              <span class="filter-label">Սկսած</span>
              <button class="select-trigger" type="button" :class="{ selected: isSelected('year_from'), open: openFilter === 'year_from' }" :aria-expanded="openFilter === 'year_from'" @click="toggleFilter('year_from')">
                <span class="select-value">{{ filterLabel('year_from') }}</span>
                <svg class="select-chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                  <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>
              <div v-if="openFilter === 'year_from'" class="filter-popover slim">
                <button v-for="year in yearFromOptions" :key="year" class="filter-option" type="button" :class="{ active: search.year_from === year }" @click="setFilter('year_from', year)">
                  <span>{{ year }}</span>
                  <span class="filter-check" />
                </button>
              </div>
            </label>
            <label :class="{ 'filter-active': openFilter === 'year_to' }">
              <span class="filter-label">Մինչև</span>
              <button class="select-trigger" type="button" :class="{ selected: isSelected('year_to'), open: openFilter === 'year_to' }" :aria-expanded="openFilter === 'year_to'" @click="toggleFilter('year_to')">
                <span class="select-value">{{ filterLabel('year_to') }}</span>
                <svg class="select-chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                  <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>
              <div v-if="openFilter === 'year_to'" class="filter-popover slim">
                <button v-for="year in yearToOptions" :key="year" class="filter-option" type="button" :class="{ active: search.year_to === year }" @click="setFilter('year_to', year)">
                  <span>{{ year }}</span>
                  <span class="filter-check" />
                </button>
              </div>
            </label>
            <button class="btn primary" type="submit">{{ t('btn.search') }}</button>
          </div>
        </form>
      </div>
    </section>

    <section class="container">
      <div class="promo-banner">
        <div>
          <h2>Մեր առկա մեքենաները</h2>
          <p>Կարող եք ընտրել մեքենա աճուրդից կամ առկա վաճառքից, ստանալ պատմության ստուգում և վերջնական արժեքի հաշվարկ։</p>
          <NuxtLink class="btn primary" :to="`/${lang}/inventory`">{{ t('btn.search') }}</NuxtLink>
        </div>
        <img src="/assets/extracted/17-28948be78c64.png" alt="Red Mazda">
      </div>
    </section>

    <section class="section compact">
      <div class="container">
        <div class="section-head">
          <h2>{{ localize(sectionTitles.featured) || t('home.featured') }}</h2>
        </div>
        <div class="grid home-cars-grid">
          <VehicleCard v-for="vehicle in featuredVehicles" :key="vehicle.id" :vehicle="vehicle" />
        </div>
      </div>
    </section>

    <section class="section compact">
      <div class="container">
        <div class="section-head">
          <h2>{{ localize(sectionTitles.auctions) || t('home.auctions') }}</h2>
        </div>
        <div class="grid auction-lot-grid">
          <NuxtLink v-for="(auction, index) in renderedAuctionCards" :key="auction.id || auction.name" class="auction-lot-card" :to="`/${lang}/auctions`">
            <span class="auction-lot-main">
              <span class="auction-lot-media">
                <img :src="auctionImage(auction)" :alt="auction.name">
              </span>
              <span class="auction-lot-content">
                <strong>{{ auction.name }}</strong>
                <small>{{ auction.address }}</small>
              </span>
            </span>
            <span class="auction-card-meta">
              <span class="auction-day">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                  <path d="M17.5 14.375V7.5H2.5V14.375C2.5 15.2038 2.82924 15.9987 3.41529 16.5847C4.00134 17.1708 4.7962 17.5 5.625 17.5H14.375C15.2038 17.5 15.9987 17.1708 16.5847 16.5847C17.1708 15.9987 17.5 15.2038 17.5 14.375ZM17.5 5.625C17.5 4.7962 17.1708 4.00134 16.5847 3.41529C15.9987 2.82924 15.2038 2.5 14.375 2.5H5.625C4.7962 2.5 4.00134 2.82924 3.41529 3.41529C2.82924 4.00134 2.5 4.7962 2.5 5.625V6.25H17.5V5.625Z" fill="currentColor" />
                </svg>
                <span>{{ auctionDate(auction.starts_at) }}</span>
              </span>
              <span class="auction-countdown">{{ auctionCountdown(auction.starts_at) }}</span>
              <span class="auction-vehicles">{{ auction.lots_count }} մեքենա</span>
              <span class="auction-open" :class="{ primary: index === 0 }">{{ t('btn.details') }}</span>
            </span>
          </NuxtLink>
        </div>
        <NuxtLink class="link-more" :to="`/${lang}/auctions`">Տեսնել բոլորը</NuxtLink>
      </div>
    </section>

    <section class="section compact soft">
      <div class="container">
        <div class="section-head">
          <h2>{{ localize(sectionTitles.private) || t('home.private') }}</h2>
        </div>
        <div class="grid home-cars-grid four">
          <VehicleCard v-for="vehicle in privateVehicles" :key="vehicle.id" :vehicle="vehicle" />
        </div>
        <NuxtLink class="link-more" :to="{ path: `/${lang}/inventory`, query: { private_sale: 1 } }">Տեսնել ամբողջ տեսականին</NuxtLink>
      </div>
    </section>

    <section class="section compact brand-section">
      <div class="container">
        <div class="section-head">
          <h2>{{ localize(sectionTitles.brands) || t('home.brands') }}</h2>
        </div>
        <div class="brand-slider-window">
          <div class="brand-strip" :style="brandTrackStyle">
            <button v-for="brand in makeOptions" :key="brand" type="button" @click="goBrand(brand)">
              <img v-if="renderedBrandLogos[brand]" :src="renderedBrandLogos[brand]" :alt="brand">
              <span v-else>{{ brand }}</span>
            </button>
          </div>
        </div>
        <div class="brand-controls">
          <button type="button" :disabled="brandSlide === 0" aria-label="Previous brands" @click="moveBrandSlider(-1)">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M11.25 13.5L6.75 9L11.25 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
          </button>
          <button type="button" :disabled="brandSlide === maxBrandSlide" aria-label="Next brands" @click="moveBrandSlider(1)">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M6.75 13.5L11.25 9L6.75 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
          </button>
        </div>
      </div>
    </section>

    <section class="section compact service-band">
      <div class="container service-layout">
        <div class="service-copy">
          <h2>{{ localize(sectionTitles.services) || t('home.services') }}</h2>
          <p class="muted">IAAI և Copart աճուրդներից մինչև բեռնափոխադրում, մաքսազերծում և հանձնում՝ բոլոր քայլերը մեկ թիմի հետ։</p>
          <NuxtLink class="link-more" :to="`/${lang}/services`">Տեսնել բոլոր ծառայությունները</NuxtLink>
        </div>
        <div class="grid service-card-grid">
          <article v-for="service in renderedServiceCards" :key="localize(service.title) || service.title" class="info-card service-card">
            <img class="service-icon" :src="service.icon" :alt="service.title">
            <h3>{{ localize(service.title) || service.title }}</h3>
            <p>{{ localize(service.text) || service.text }}</p>
          </article>
        </div>
      </div>
    </section>

    <section class="section compact">
      <div class="container trust-block">
        <h2>Ինչպես գնել մեքենա աճուրդից</h2>
        <p class="muted">Ընտրեք մեքենան, ուղարկեք հայտ, հաստատեք արժեքը և հետևեք առաքման ամբողջ ընթացքին։</p>
        <NuxtLink class="link-more" :to="`/${lang}/how-to-buy`">Իմանալ ավելին</NuxtLink>
        <div class="home-stats">
          <div><strong>190+</strong><span>Մատակարար տարբեր երկրներում</span></div>
          <div><strong>2,5 մլն</strong><span>Աճուրդային մեքենաներ</span></div>
          <div><strong>3,000</strong><span>Աշխատակից ողջ աշխարհում</span></div>
        </div>
      </div>
    </section>
  </main>
</template>
