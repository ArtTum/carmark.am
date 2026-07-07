<script setup lang="ts">
const lang = useLang();
const t = useT();

const search = reactive({
  make: '',
  model: '',
  year_from: '',
  year_to: '',
});

const openFilter = ref('');
const filterQuery = ref('');
type SearchField = 'make' | 'model' | 'year_from' | 'year_to';

const defaultYearFrom = '1960';
const defaultYearTo = '2024';
const defaultMakes = ['Ford', 'Chevrolet', 'Nissan', 'Honda', 'BMW', 'Jeep', 'Hyundai', 'Mercedes-Benz', 'Volkswagen', 'KIA'];
const defaultModelsByMake: Record<string, string[]> = {
  BMW: ['3 Series', '5 Series', 'X3', 'X5', 'M4'],
  Chevrolet: ['Malibu', 'Cruze', 'Equinox', 'Tahoe', 'Silverado'],
  Ford: ['Focus', 'Fusion', 'Escape', 'Explorer', 'F-150'],
  Honda: ['Civic', 'Accord', 'CR-V', 'Pilot'],
  Hyundai: ['Elantra', 'Sonata', 'Tucson', 'Santa Fe'],
  Jeep: ['Cherokee', 'Grand Cherokee', 'Wrangler', 'Compass'],
  KIA: ['Forte', 'Optima', 'Sportage', 'Sorento'],
  'Mercedes-Benz': ['C-Class', 'E-Class', 'GLC', 'GLE'],
  Nissan: ['Altima', 'Sentra', 'Rogue', 'Pathfinder'],
  Volkswagen: ['Jetta', 'Passat', 'Tiguan', 'Golf'],
};
const defaultModels = Array.from(new Set(Object.values(defaultModelsByMake).flat()));
const yearFromOptions = ['1960', '1970', '1980', '1990', '2000', '2010', '2015', '2018', '2020', '2022'];
const yearToOptions = ['2024', '2023', '2022', '2021', '2020', '2019', '2018', '2015', '2010', '2000'];

function uniqueOptions(...groups: Array<Array<string | null | undefined>>) {
  return Array.from(new Set(groups.flat().map((item) => String(item || '').trim()).filter(Boolean)));
}

const { data: vehicleData } = await useFetch<any>(apiUrl('/vehicles'));
const vehicles = computed(() => vehicleData.value?.data || []);
const filters = computed(() => vehicleData.value?.filters || {});

type HomeVehicleSeed = {
  id: string;
  slug: string;
  title: string;
  images: string[];
  lot: string;
  auction: string;
  sale_date: string;
  location: string;
  current_bid: number;
  year: number;
  make: string;
  model: string;
  body: string;
  fuel: string;
};

const featuredVehicleSeeds: HomeVehicleSeed[] = [
  {
    id: 'home-featured-kia',
    slug: 'toyota-sequoia-2001',
    title: '2017 Kia Optima LX',
    images: [
      '/assets/home/showcase/2017-kia-optima-lx.jpg',
      '/assets/home/showcase/2023-toyota-prius.jpg',
      '/assets/home/showcase/2018-chevrolet-malibu-lt.png',
      '/assets/home/showcase/2022-chevrolet-bolt-ev.jpg',
      '/assets/home/showcase/2021-chevrolet-silverado.jpg',
    ],
    lot: '38217442',
    auction: 'Copart',
    sale_date: '2026-07-09 10:00:00',
    location: 'Los Angeles - CA',
    current_bid: 5025,
    year: 2017,
    make: 'KIA',
    model: 'Optima LX',
    body: 'Sedan',
    fuel: 'Gasoline',
  },
  {
    id: 'home-featured-bolt',
    slug: 'chevrolet-bolt-ev-2022',
    title: '2022 Chevrolet Bolt EV FWD 2LT',
    images: [
      '/assets/home/showcase/2022-chevrolet-bolt-ev.jpg',
      '/assets/home/showcase/2018-chevrolet-malibu-lt.png',
      '/assets/home/showcase/2023-hyundai-sonata-sel.png',
      '/assets/home/showcase/2023-toyota-prius.jpg',
      '/assets/home/showcase/2017-kia-optima-lx.jpg',
    ],
    lot: '38303409',
    auction: 'IAAI',
    sale_date: '2026-07-10 11:00:00',
    location: 'Los Angeles - CA',
    current_bid: 5025,
    year: 2022,
    make: 'Chevrolet',
    model: 'Bolt EV',
    body: 'Hatchback',
    fuel: 'Electric',
  },
  {
    id: 'home-featured-malibu',
    slug: 'chevrolet-malibu-lt-2018',
    title: '2018 Chevrolet Malibu LT',
    images: [
      '/assets/home/showcase/2018-chevrolet-malibu-lt.png',
      '/assets/home/showcase/2022-chevrolet-bolt-ev.jpg',
      '/assets/home/showcase/2021-chevrolet-silverado.jpg',
      '/assets/home/showcase/2017-kia-optima-lx.jpg',
      '/assets/home/showcase/2023-hyundai-sonata-sel.png',
    ],
    lot: '38251373',
    auction: 'IAAI',
    sale_date: '2026-07-11 09:30:00',
    location: 'Atlanta North - GA',
    current_bid: 5025,
    year: 2018,
    make: 'Chevrolet',
    model: 'Malibu LT',
    body: 'Sedan',
    fuel: 'Gasoline',
  },
];

const privateVehicleSeeds: HomeVehicleSeed[] = [
  {
    id: 'home-private-sonata',
    slug: 'hyundai-sonata-sel-2023',
    title: '2023 Hyundai Sonata SEL',
    images: [
      '/assets/home/showcase/2023-hyundai-sonata-sel.png',
      '/assets/home/showcase/2023-toyota-prius.jpg',
      '/assets/home/showcase/2022-chevrolet-bolt-ev.jpg',
      '/assets/home/showcase/2018-chevrolet-malibu-lt.png',
      '/assets/home/showcase/2017-kia-optima-lx.jpg',
    ],
    lot: '38317147',
    auction: 'IAAI',
    sale_date: '2026-07-12 10:00:00',
    location: 'Atlanta North - GA',
    current_bid: 25000,
    year: 2023,
    make: 'Hyundai',
    model: 'Sonata SEL',
    body: 'Sedan',
    fuel: 'Gasoline',
  },
  {
    id: 'home-private-ram',
    slug: 'ram-2500-tradesman-2018',
    title: '2018 Ram 2500 Tradesman 4x4',
    images: [
      '/assets/home/showcase/2018-ram-2500-tradesman.png',
      '/assets/home/showcase/2021-chevrolet-silverado.jpg',
      '/assets/home/showcase/2023-toyota-prius.jpg',
      '/assets/home/showcase/2017-kia-optima-lx.jpg',
      '/assets/home/showcase/2022-chevrolet-bolt-ev.jpg',
    ],
    lot: '38318729',
    auction: 'Copart',
    sale_date: '2026-07-13 08:00:00',
    location: 'Ashland - KY',
    current_bid: 25000,
    year: 2018,
    make: 'Ram',
    model: '2500 Tradesman',
    body: 'Pickup',
    fuel: 'Gasoline',
  },
  {
    id: 'home-private-silverado',
    slug: 'dodge-charger-gt-2022',
    title: '2021 Chevrolet Silverado 1500',
    images: [
      '/assets/home/showcase/2021-chevrolet-silverado.jpg',
      '/assets/home/showcase/2018-ram-2500-tradesman.png',
      '/assets/home/showcase/2023-toyota-prius.jpg',
      '/assets/home/showcase/2018-chevrolet-malibu-lt.png',
      '/assets/home/showcase/2022-chevrolet-bolt-ev.jpg',
    ],
    lot: '38319421',
    auction: 'IAAI',
    sale_date: '2026-07-14 09:00:00',
    location: 'Sacramento - CA',
    current_bid: 25000,
    year: 2021,
    make: 'Chevrolet',
    model: 'Silverado 1500',
    body: 'Pickup',
    fuel: 'Gasoline',
  },
  {
    id: 'home-private-prius',
    slug: 'chevrolet-bolt-ev-2022',
    title: '2023 Toyota Prius XLE AWD-E',
    images: [
      '/assets/home/showcase/2023-toyota-prius.jpg',
      '/assets/home/showcase/2017-kia-optima-lx.jpg',
      '/assets/home/showcase/2022-chevrolet-bolt-ev.jpg',
      '/assets/home/showcase/2023-hyundai-sonata-sel.png',
      '/assets/home/showcase/2018-chevrolet-malibu-lt.png',
    ],
    lot: '38321590',
    auction: 'Copart',
    sale_date: '2026-07-15 12:00:00',
    location: 'Avenel - NJ',
    current_bid: 25000,
    year: 2023,
    make: 'Toyota',
    model: 'Prius XLE',
    body: 'Hatchback',
    fuel: 'Hybrid',
  },
];

function homeVehicle(seed: HomeVehicleSeed) {
  const source = vehicles.value.find((vehicle: any) => vehicle.slug === seed.slug) || {};
  const gallery = uniqueOptions(seed.images, source.images || []);

  return {
    ...source,
    ...seed,
    title: { hy: seed.title, en: seed.title, ru: seed.title },
    images: gallery,
    private_sale: true,
    status: 'published',
  };
}

const featuredVehicles = computed(() => featuredVehicleSeeds.map(homeVehicle));
const fallbackPrivateVehicles = computed(() => privateVehicleSeeds.map(homeVehicle));
const auctionCards = computed(() => [
  {
    id: 'home-auction-ashland',
    name: 'Ashland (KY)',
    address: '123 Four Wheel Dr, Ashland, KY 41102',
    starts_at: '2026-07-09 10:00:00',
    lots_count: 556,
    image: '/assets/home/showcase/auction-ashland.png',
  },
  {
    id: 'home-auction-los-angeles',
    name: 'Public car auction in Los Angeles - CA',
    address: '18300 S. Vermont Ave, Gardena, CA 90248',
    starts_at: '2026-07-10 11:30:00',
    lots_count: 556,
    image: '/assets/home/showcase/auction-los-angeles.png',
  },
  {
    id: 'home-auction-atlanta',
    name: 'Atlanta North (GA)',
    address: '6242 Blackacre Trl. N.W, Acworth, GA 30101',
    starts_at: '2026-07-11 10:00:00',
    lots_count: 556,
    image: '/assets/home/showcase/auction-atlanta-north.png',
  },
]);

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

const makeOptions = computed(() => uniqueOptions(filters.value.makes || [], vehicles.value.map((vehicle: any) => vehicle.make), defaultMakes));

const brandStripDefaults = ['Ford', 'Nissan', 'Honda', 'Dodge', 'Mercedes-Benz', 'Toyota', 'Mazda', 'Audi'];
const brandNames = computed(() => uniqueOptions(brandStripDefaults, makeOptions.value).slice(0, 12));
const brandSliderWindow = ref<HTMLElement | null>(null);
const brandViewportWidth = ref(0);
const brandSlide = ref(0);
const brandCardStep = 110;
const brandVisibleSlots = computed(() => Math.max(1, Math.floor((brandViewportWidth.value + 14) / brandCardStep)));
const maxBrandSlide = computed(() => Math.max(0, brandNames.value.length - brandVisibleSlots.value));
const brandTrackStyle = computed(() => ({ '--brand-slide': String(brandSlide.value) }));

const serviceCards = [
  {
    title: 'Դեպոզիտի վճարում',
    text: 'Նախնական աճուրդի գնի 15%-ի չափով, բայց ոչ պակաս քան 500.000 դրամ։',
    icon: '/assets/home/services/deposit.svg',
  },
  {
    title: 'Մեքենայի պատմություն',
    text: 'Նախքան աճուրդին մասնակցելը, ստուգեք ձեր նախընտրած ավտոմեքենայի պատմությունը։',
    icon: '/assets/home/services/history.svg',
  },
  {
    title: 'Աճուրդի մասնակցություն',
    text: 'Օնլայն հետևելով լայվ աճուրդին, գնեք մեքենան ձեր նախընտրած գնով։',
    icon: '/assets/home/services/auction.svg',
  },
  {
    title: 'Պայմանագրի կնքում',
    text: 'Պայմանագրում ամրագրվում է մեքենայի վերջնական արժեքը ՀՀ-ում, կողմերի իրավունքները։',
    icon: '/assets/home/services/contract.svg',
  },
];

watch(brandNames, () => {
  if (brandSlide.value > maxBrandSlide.value) brandSlide.value = maxBrandSlide.value;
});

watch(maxBrandSlide, () => {
  if (brandSlide.value > maxBrandSlide.value) brandSlide.value = maxBrandSlide.value;
});

function updateBrandViewport() {
  brandViewportWidth.value = brandSliderWindow.value?.clientWidth || 0;
}

onMounted(() => {
  updateBrandViewport();
  window.addEventListener('resize', updateBrandViewport);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateBrandViewport);
});

const homeCopy = computed(() => ({
  heroTitle: {
    hy: 'Ապահովագրական Ավտո Աճուրդ',
    en: 'Insurance Auto Auctions',
    ru: 'Страховые автоаукционы',
  }[lang.value],
  heroText: {
    hy: 'Առաջարկներ ԱՄՆ-ի առաջատար աճուրդներից՝ գնման, հաշվարկի և առաքման ամբողջական ուղեկցմամբ։',
    en: 'Cars from leading US auctions with purchase, estimate and delivery support.',
    ru: 'Авто с ведущих аукционов США с сопровождением покупки, расчета и доставки.',
  }[lang.value],
  promoTitle: {
    hy: 'Մեր առկա մեքենաները',
    en: 'Our Available Vehicles',
    ru: 'Автомобили в наличии',
  }[lang.value],
  promoText: {
    hy: 'Գտեք Ձեր մեքենան աճուրդից կամ առկա վաճառքից, ստացեք ամբողջական պատմություն ու վերջնական արժեքը։',
    en: 'Find a car from auction or private inventory and see its history and landed cost.',
    ru: 'Подберите авто с аукциона или из наличия и получите историю и итоговую стоимость.',
  }[lang.value],
}));

const modelOptions = computed(() => {
  const apiModels = uniqueOptions(filters.value.models || []);
  const vehicleModels = uniqueOptions(vehicles.value.map((vehicle: any) => vehicle.model));
  if (!search.make) return uniqueOptions(apiModels, vehicleModels, defaultModels);

  const matchingVehicleModels = uniqueOptions(vehicles.value
    .filter((vehicle: any) => vehicle.make === search.make)
    .map((vehicle: any) => vehicle.model)
    .filter(Boolean));
  const fallbackModels = defaultModelsByMake[search.make] || [];

  return uniqueOptions(matchingVehicleModels, fallbackModels, matchingVehicleModels.length || fallbackModels.length ? [] : apiModels);
});

const filteredMakes = computed(() => {
  const query = filterQuery.value.trim().toLowerCase();
  const makes = makeOptions.value;
  if (openFilter.value !== 'make' || !query) return makes;
  return makes.filter((make: string) => make.toLowerCase().includes(query));
});

const filteredModels = computed(() => {
  const query = filterQuery.value.trim().toLowerCase();
  const models = modelOptions.value || [];
  if (openFilter.value !== 'model' || !query) return models;
  return models.filter((model: string) => model.toLowerCase().includes(query));
});

function toggleFilter(name: string) {
  openFilter.value = openFilter.value === name ? '' : name;
  filterQuery.value = '';
}

function filterLabel(field: SearchField) {
  const defaults: Record<string, string> = {
    make: lang.value === 'hy' ? 'Բոլոր մակնիշները' : 'All makes',
    model: lang.value === 'hy' ? 'Բոլոր մոդելները' : 'All models',
    year_from: defaultYearFrom,
    year_to: defaultYearTo,
  };

  return search[field] || defaults[field];
}

function setFilter(field: SearchField, value: string) {
  search[field] = value;
  if (field === 'make') search.model = '';
  openFilter.value = '';
  filterQuery.value = '';
}

function isSelected(field: SearchField) {
  return Boolean(search[field]);
}

const cleanSearch = computed(() => {
  const query = Object.fromEntries(Object.entries(search).filter(([, value]) => value));
  return {
    year_from: search.year_from || defaultYearFrom,
    year_to: search.year_to || defaultYearTo,
    ...query,
  };
});

function submitSearch() {
  return navigateTo({ path: `/${lang.value}/inventory`, query: cleanSearch.value });
}

function goBrand(make: string) {
  return navigateTo({ path: `/${lang.value}/inventory`, query: { make } });
}

function moveBrandSlider(direction: number) {
  brandSlide.value = Math.min(maxBrandSlide.value, Math.max(0, brandSlide.value + direction));
}

function auctionDate(value: string) {
  if (!value) return '';
  const date = new Date(value);
  if (lang.value === 'hy') {
    const months = ['Հուն', 'Փետ', 'Մար', 'Ապր', 'Մայ', 'Հուն', 'Հուլ', 'Օգս', 'Սեպ', 'Հոկ', 'Նոյ', 'Դեկ'];
    return `${months[date.getMonth()]} ${date.getDate()}`;
  }

  return new Intl.DateTimeFormat(lang.value === 'ru' ? 'ru-RU' : 'en-US', { month: 'short', day: 'numeric' }).format(date);
}
</script>

<template>
  <main class="home-page">
    <section class="home-hero">
      <div class="container hero-shell">
        <p class="breadcrumb">{{ t('nav.buy') }}</p>
        <h1>{{ homeCopy.heroTitle }}</h1>
        <p>{{ homeCopy.heroText }}</p>

        <form class="hero-search-card" @submit.prevent="submitSearch">
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
                <button v-for="year in yearFromOptions" :key="year" class="filter-option" type="button" :class="{ active: search.year_from === year || (!search.year_from && year === defaultYearFrom) }" @click="setFilter('year_from', year)">
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
                <button v-for="year in yearToOptions" :key="year" class="filter-option" type="button" :class="{ active: search.year_to === year || (!search.year_to && year === defaultYearTo) }" @click="setFilter('year_to', year)">
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
          <h2>{{ homeCopy.promoTitle }}</h2>
          <p>{{ homeCopy.promoText }}</p>
          <NuxtLink class="btn primary" :to="`/${lang}/inventory`">{{ t('btn.search') }}</NuxtLink>
        </div>
        <img src="/assets/extracted/17-28948be78c64.png" alt="Red Mazda">
      </div>
    </section>

    <section class="section compact">
      <div class="container">
        <div class="section-head">
          <h2>{{ t('home.featured') }}</h2>
        </div>
        <div class="grid home-cars-grid">
          <VehicleCard v-for="vehicle in featuredVehicles" :key="vehicle.id" :vehicle="vehicle" />
        </div>
      </div>
    </section>

    <section class="section compact">
      <div class="container">
        <div class="section-head">
          <h2>{{ t('home.auctions') }}</h2>
        </div>
        <div class="grid auction-lot-grid">
          <NuxtLink v-for="auction in auctionCards" :key="auction.id" class="auction-lot-card" :to="`/${lang}/auctions`">
            <img :src="auction.image" :alt="auction.name">
            <span class="auction-lot-content">
              <strong>{{ auction.name }}</strong>
              <small>{{ auction.address }}</small>
              <em class="auction-card-meta">
                <span class="auction-date">
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M17.5 14.375V7.5H2.5V14.375C2.5 15.2038 2.82924 15.9987 3.41529 16.5847C4.00134 17.1708 4.7962 17.5 5.625 17.5H14.375C15.2038 17.5 15.9987 17.1708 16.5847 16.5847C17.1708 15.9987 17.5 15.2038 17.5 14.375ZM7.49625 10.31C7.49625 10.5581 7.39768 10.7961 7.22221 10.9716C7.04675 11.1471 6.80877 11.2456 6.56063 11.2456C6.31248 11.2456 6.0745 11.1471 5.89904 10.9716C5.72357 10.7961 5.625 10.5581 5.625 10.31C5.625 10.0619 5.72357 9.82388 5.89904 9.64841C6.0745 9.47295 6.31248 9.37437 6.56063 9.37437C6.80877 9.37437 7.04675 9.47295 7.22221 9.64841C7.39768 9.82388 7.49625 10.0619 7.49625 10.31ZM7.49625 13.435C7.49625 13.6831 7.39768 13.9211 7.22221 14.0966C7.04675 14.2721 6.80877 14.3706 6.56063 14.3706C6.31248 14.3706 6.0745 14.2721 5.89904 14.0966C5.72357 13.9211 5.625 13.6831 5.625 13.435C5.625 13.1869 5.72357 12.9489 5.89904 12.7734C6.0745 12.5979 6.31248 12.4994 6.56063 12.4994C6.80877 12.4994 7.04675 12.5979 7.22221 12.7734C7.39768 12.9489 7.49625 13.1869 7.49625 13.435ZM10.9362 10.31C10.9362 10.5581 10.8377 10.7961 10.6622 10.9716C10.4867 11.1471 10.2488 11.2456 10.0006 11.2456C9.75248 11.2456 9.5145 11.1471 9.33904 10.9716C9.16357 10.7961 9.065 10.5581 9.065 10.31C9.065 10.0619 9.16357 9.82388 9.33904 9.64841C9.5145 9.47295 9.75248 9.37437 10.0006 9.37437C10.2488 9.37437 10.4867 9.47295 10.6622 9.64841C10.8377 9.82388 10.9362 10.0619 10.9362 10.31ZM10.9362 13.435C10.9362 13.6831 10.8377 13.9211 10.6622 14.0966C10.4867 14.2721 10.2488 14.3706 10.0006 14.3706C9.75248 14.3706 9.5145 14.2721 9.33904 14.0966C9.16357 13.9211 9.065 13.6831 9.065 13.435C9.065 13.1869 9.16357 12.9489 9.33904 12.7734C9.5145 12.5979 9.75248 12.4994 10.0006 12.4994C10.2488 12.4994 10.4867 12.5979 10.6622 12.7734C10.8377 12.9489 10.9362 13.1869 10.9362 13.435ZM14.3713 10.31C14.3713 10.5581 14.2727 10.7961 14.0972 10.9716C13.9217 11.1471 13.6838 11.2456 13.4356 11.2456C13.1875 11.2456 12.9495 11.1471 12.774 10.9716C12.5986 10.7961 12.5 10.5581 12.5 10.31C12.5 10.0619 12.5986 9.82388 12.774 9.64841C12.9495 9.47295 13.1875 9.37437 13.4356 9.37437C13.6838 9.37437 13.9217 9.47295 14.0972 9.64841C14.2727 9.82388 14.3713 10.0619 14.3713 10.31ZM17.5 5.625C17.5 4.7962 17.1708 4.00134 16.5847 3.41529C15.9987 2.82924 15.2038 2.5 14.375 2.5H5.625C4.7962 2.5 4.00134 2.82924 3.41529 3.41529C2.82924 4.00134 2.5 4.7962 2.5 5.625V6.25H17.5V5.625Z" fill="currentColor" />
                  </svg>
                  {{ auctionDate(auction.starts_at) }}
                </span>
                <span>{{ auction.lots_count }} մեքենա</span>
                <span class="auction-open">Բացել</span>
              </em>
            </span>
          </NuxtLink>
        </div>
        <NuxtLink class="link-more" :to="`/${lang}/auctions`">Տեսնել բոլորը</NuxtLink>
      </div>
    </section>

    <section class="section compact soft">
      <div class="container">
        <div class="section-head">
          <h2>{{ t('home.private') }}</h2>
        </div>
        <div class="grid home-cars-grid four">
          <VehicleCard v-for="vehicle in fallbackPrivateVehicles" :key="vehicle.id" :vehicle="vehicle" />
        </div>
        <NuxtLink class="link-more" :to="{ path: `/${lang}/inventory`, query: { private_sale: 1 } }">Տեսնել ամբողջ տեսականին</NuxtLink>
      </div>
    </section>

    <section class="section compact brand-section">
      <div class="container">
        <div class="section-head">
          <h2>{{ t('home.brands') }}</h2>
        </div>
        <div ref="brandSliderWindow" class="brand-slider-window">
          <div class="brand-strip" :style="brandTrackStyle">
            <button v-for="brand in brandNames" :key="brand" type="button" @click="goBrand(brand)">
              <img v-if="brandLogos[brand]" :src="brandLogos[brand]" :alt="brand">
              <span v-else>{{ brand }}</span>
            </button>
          </div>
        </div>
        <div class="brand-controls">
          <button type="button" :disabled="brandSlide === 0" aria-label="Previous brands" @click="moveBrandSlider(-1)">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
              <path d="M11.25 13.5L6.75 9L11.25 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <button type="button" :disabled="brandSlide === maxBrandSlide" aria-label="Next brands" @click="moveBrandSlider(1)">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
              <path d="M6.75 13.5L11.25 9L6.75 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>
      </div>
    </section>

    <section class="section compact service-band">
      <div class="container service-layout">
        <div class="service-copy">
          <h2>{{ t('home.services') }}</h2>
          <p class="muted">IAAI աճուրդներում ներկայացված մեքենաների ցանկը թարմացվում է ամեն օր՝ ապահովելով պահանջված ավտոմեքենա ընտրության հնարավորությունը։</p>
          <NuxtLink class="link-more" :to="`/${lang}/services`">Տեսնել բոլոր ծառայությունները</NuxtLink>
        </div>
        <div class="grid service-card-grid">
          <article v-for="service in serviceCards" :key="service.title" class="info-card service-card">
            <img class="service-icon" :src="service.icon" :alt="service.title">
            <h3>{{ service.title }}</h3>
            <p>{{ service.text }}</p>
          </article>
        </div>
      </div>
    </section>

    <section class="section compact">
      <div class="container trust-block">
        <h2>Ինչպե՞ս գնել մեքենա աճուրդից</h2>
        <p class="muted">Ընտրեք մեքենան, ուղարկեք հայտ, հաստատեք արժեքը և հետևեք առաքման ամբողջ ընթացքին։</p>
        <NuxtLink class="link-more" :to="`/${lang}/how-to-buy`">Իմանալ ավելին</NuxtLink>
        <div class="home-stats">
          <div>
            <strong>190+</strong>
            <span>Մատակարար տարբեր երկրներում</span>
          </div>
          <div>
            <strong>2,5 մլն</strong>
            <span>Աճուրդային մեքենաներ</span>
          </div>
          <div>
            <strong>3,000</strong>
            <span>Աշխատակից ողջ աշխարհում</span>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>
