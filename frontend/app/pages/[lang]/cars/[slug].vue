<script setup lang="ts">
const t = useT();
const lang = useLang();
const route = useRoute();

const bidSent = ref(false);
const callbackSent = ref(false);
const bidPending = ref(false);
const callbackPending = ref(false);
const bidError = ref('');
const callbackError = ref('');
const saved = ref(false);
const shareNote = ref('');
const activeImage = ref(0);
const galleryModalOpen = ref(false);
const activeInfoTab = ref<'calculator' | 'details'>('details');
const bid = reactive({ amount: 300, name: '', email: '' });
const callback = reactive({ first_name: '', last_name: '', email: '' });
const calculatorForm = reactive({
  purchasePrice: 8300,
  auctionFee: 200,
  transportFrom: 'MN - Long Island',
  transportTo: 'Վրաստան / Փոթի',
  auction: 'Copart',
  year: 2000,
  engine: 'Բենզին',
  engineSize: 4500,
});

const fallbackVehicle = {
  id: 1,
  slug: 'toyota-sequoia-2001',
  title: { hy: '2001 TOYOTA SEQUOIA 4.7L FOR SALE IN WALDORF', en: '2001 TOYOTA SEQUOIA 4.7L FOR SALE IN WALDORF', ru: '2001 TOYOTA SEQUOIA 4.7L FOR SALE IN WALDORF' },
  lot: '222555555555',
  vin: 'LVVV6488498M555',
  year: 2001,
  make: 'Toyota',
  model: 'Sequoia',
  body: 'SEDAN 4 DR',
  engine: '4.7 L 8',
  transmission: 'Automatic',
  fuel: 'Բենզին',
  color: 'Սպիտակ',
  condition: 'Նկատելի վնաս',
  location: 'Waldorf - MD',
  auction: 'IAAI',
  sale_date: '2026-07-15 09:30:00',
  odometer: '117,630 կմ',
  current_bid: 5025,
  buy_now: 25500,
  shipping_fee: 275,
  images: [
    '/assets/extracted/03-17064740bf1e.jpg',
    '/assets/extracted/14-28b124d87f27.png',
    '/assets/extracted/18-a93576670a23.jpg',
    '/assets/extracted/04-214f9796b0b8.jpg',
    '/assets/extracted/16-dcd4befb4127.png',
  ],
};

const fallbackRelated = [
  {
    id: 'rel-1',
    slug: 'toyota-sequoia-2001',
    title: { hy: '2017 Kia Optima LX', en: '2017 Kia Optima LX', ru: '2017 Kia Optima LX' },
    images: ['/assets/home/showcase/2017-kia-optima-lx.jpg'],
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
    id: 'rel-2',
    slug: 'chevrolet-bolt-ev-2022',
    title: { hy: '2022 Chevrolet Bolt EV FWD 2LT', en: '2022 Chevrolet Bolt EV FWD 2LT', ru: '2022 Chevrolet Bolt EV FWD 2LT' },
    images: ['/assets/home/showcase/2022-chevrolet-bolt-ev.jpg'],
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
    id: 'rel-3',
    slug: 'chevrolet-malibu-lt-2018',
    title: { hy: '2018 Chevrolet Malibu LT', en: '2018 Chevrolet Malibu LT', ru: '2018 Chevrolet Malibu LT' },
    images: ['/assets/home/showcase/2018-chevrolet-malibu-lt.png'],
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
];

const { data } = await useFetch<any>(apiUrl(`/vehicles/${route.params.slug}`));
const vehicle = computed(() => data.value?.data || fallbackVehicle);
const related = computed(() => data.value?.related?.length ? data.value.related : fallbackRelated);
const images = computed(() => (vehicle.value.images?.length ? vehicle.value.images : fallbackVehicle.images));
const mainImage = computed(() => images.value[activeImage.value] || images.value[0]);
const title = computed(() => localize(vehicle.value.title));

const detailsLeft = computed(() => [
  ['Լոտի համար', vehicle.value.lot],
  ['VIN', vehicle.value.vin],
  ['Օդոմետր', vehicle.value.odometer],
  ['Վնասված մաս', vehicle.value.condition],
  ['Տեսակ', vehicle.value.body],
  ['Թափք', vehicle.value.body],
  ['Փոխանցման տուփ', vehicle.value.transmission],
]);

const detailsRight = computed(() => [
  ['Շարժիչ', vehicle.value.engine],
  ['Քարշակ', 'Հետևի անիվ'],
  ['Վառելիք', vehicle.value.fuel],
  ['Cylinders', '6 Cylinders'],
  ['Գույնը', vehicle.value.color],
  ['Գնվելու վայրը', vehicle.value.location],
]);

const saleInfo = computed(() => [
  ['Հայտի կարգավիճակ', 'Հաստատված'],
  ['Վաճառքի կարգավիճակ', 'Նկատագրային հայտ'],
  ['Ընթացիկ հայտ', money(vehicle.value.current_bid)],
  ['Ժամանակ', '3ժ. 22ր. 10վ.'],
  ['Վաճառքի ամսաթիվ', vehicle.value.sale_date?.slice(0, 16).replace('T', ' ')],
  ['Line / Run', 'item'],
  ['Թարմացվել է', '11 / 24 / 23, 7:30PM'],
]);

const transportFromOptions = ['MN - Long Island', 'CA - Los Angeles', 'GA - Atlanta', 'TX - Houston'];
const transportToOptions = ['Վրաստան / Փոթի', 'Հայաստան / Երևան', 'Վրաստան / Բաթումի'];
const auctionOptions = ['Copart', 'IAAI', 'Dealer auction'];
const yearOptions = [2026, 2024, 2022, 2020, 2018, 2015, 2010, 2000];
const engineOptions = ['Բենզին', 'Դիզել', 'Հիբրիդ', 'Էլեկտրական'];

const calculatorShipping = computed(() => Number(vehicle.value.shipping_fee || 275));
const calculatorServiceFee = computed(() => Math.round(Number(calculatorForm.purchasePrice || 0) * 0.03));
const calculatorTransportFee = computed(() => calculatorForm.transportTo.includes('Հայաստան') ? 1450 : 920);
const calculatorCustomsFee = computed(() => {
  const engineSize = Number(calculatorForm.engineSize || 0);
  const year = Number(calculatorForm.year || new Date().getFullYear());
  const ageRate = Math.max(1, new Date().getFullYear() - year) * 35;
  const engineRate = calculatorForm.engine === 'Էլեկտրական' ? 250 : Math.round(engineSize * 0.8);
  return engineRate + ageRate;
});
const calculatorTotal = computed(() => (
  Number(calculatorForm.purchasePrice || 0)
  + Number(calculatorForm.auctionFee || 0)
  + calculatorShipping.value
  + calculatorServiceFee.value
  + calculatorTransportFee.value
  + calculatorCustomsFee.value
));
const calculatorRows = computed(() => [
  ['Աճուրդում գնելու գին', money(calculatorForm.purchasePrice)],
  ['Աճուրդի միջնորդավճար', money(calculatorForm.auctionFee)],
  ['Փոխադրման վճար', money(calculatorTransportFee.value)],
  ['Մաքսազերծում', money(calculatorCustomsFee.value)],
]);
const dutyCards = computed(() => [
  {
    title: 'Բնապահպանական վճար',
    rows: [
      ['Ներմուծման վճար', money(45)],
      ['ԱԱՀ', money(45)],
      ['Մաքսային ծառայություն', money(45)],
    ],
    total: money(595),
  },
  {
    title: 'Մաքսային վճար',
    rows: [
      ['Մաքսային արժեք', money(45)],
      ['Մաքսատուրք', money(45)],
      ['Մաքսային ծառայություն', money(45)],
    ],
    total: money(123560),
  },
]);

function readStoredArray(key: string) {
  if (!import.meta.client) return [];

  try {
    const value = JSON.parse(localStorage.getItem(key) || '[]');
    return Array.isArray(value) ? value.map(String) : [];
  } catch {
    localStorage.removeItem(key);
    return [];
  }
}

function changeImage(direction: number) {
  activeImage.value = (activeImage.value + direction + images.value.length) % images.value.length;
}

function openGalleryModal(index = activeImage.value) {
  activeImage.value = index;
  galleryModalOpen.value = true;
}

function closeGalleryModal() {
  galleryModalOpen.value = false;
}

function handleGalleryKeydown(event: KeyboardEvent) {
  if (!galleryModalOpen.value) return;
  if (event.key === 'Escape') closeGalleryModal();
  if (event.key === 'ArrowLeft') changeImage(-1);
  if (event.key === 'ArrowRight') changeImage(1);
}

function toggleSaved() {
  const id = String(vehicle.value.id);
  const ids = readStoredArray('watchlist');
  const next = ids.includes(id) ? ids.filter((item: string) => item !== id) : [...ids, id];
  localStorage.setItem('watchlist', JSON.stringify(next));
  saved.value = next.includes(id);
}

async function shareVehicle() {
  if (!import.meta.client) return;

  const url = window.location.href;
  shareNote.value = '';

  try {
    if (navigator.share) {
      await navigator.share({ title: title.value, url });
      shareNote.value = 'Հղումը պատրաստ է կիսվելու։';
    } else if (navigator.clipboard) {
      await navigator.clipboard.writeText(url);
      shareNote.value = 'Հղումը պատճենվեց։';
    } else {
      shareNote.value = url;
    }
  } catch {
    shareNote.value = 'Հղումը հնարավոր չեղավ պատճենել։';
  }

  window.setTimeout(() => {
    shareNote.value = '';
  }, 2600);
}

async function submitBid() {
  if (bidPending.value) return;

  bidPending.value = true;
  bidError.value = '';
  bidSent.value = false;

  try {
    await $fetch(apiUrl('/bids'), {
      method: 'POST',
      body: {
        vehicle_id: Number(vehicle.value.id) || null,
        amount: Number(bid.amount),
        name: bid.name || 'Website visitor',
        email: bid.email || 'visitor@example.com',
      },
    });
    bidSent.value = true;
    Object.assign(bid, { amount: 300, name: '', email: '' });
  } catch {
    bidError.value = 'Հայտը հնարավոր չեղավ ուղարկել։ Փորձեք մի փոքր ուշ։';
  } finally {
    bidPending.value = false;
  }
}

async function submitCallback() {
  if (callbackPending.value) return;

  callbackPending.value = true;
  callbackError.value = '';
  callbackSent.value = false;

  try {
    await $fetch(apiUrl('/leads'), {
      method: 'POST',
      body: {
        name: `${callback.first_name} ${callback.last_name}`.trim(),
        email: callback.email,
        phone: '',
        message: `Callback request for ${title.value}`,
        vehicle_id: Number(vehicle.value.id) || null,
        type: 'vehicle_callback',
      },
    });
    callbackSent.value = true;
    Object.assign(callback, { first_name: '', last_name: '', email: '' });
  } catch {
    callbackError.value = 'Հայտը հնարավոր չեղավ ուղարկել։ Փորձեք մի փոքր ուշ։';
  } finally {
    callbackPending.value = false;
  }
}

onMounted(() => {
  saved.value = readStoredArray('watchlist').includes(String(vehicle.value.id));
  document.addEventListener('keydown', handleGalleryKeydown);
});

watch(galleryModalOpen, (open) => {
  if (!import.meta.client) return;
  document.body.style.overflow = open ? 'hidden' : '';
});

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleGalleryKeydown);
  document.body.style.overflow = '';
});
</script>

<template>
  <main class="product-page">
    <section class="section product-section">
      <div class="container product-layout">
        <div class="product-main-panel">
          <div class="product-title-row">
            <h1>{{ title }}</h1>
            <div class="product-actions">
              <button type="button" @click="shareVehicle">Կիսվել</button>
              <button type="button" :class="{ active: saved }" @click="toggleSaved">{{ saved ? 'Պահպանված է' : 'Պահպանել' }}</button>
            </div>
          </div>
          <p v-if="shareNote" class="success-note product-note">{{ shareNote }}</p>

          <div class="product-hero-grid">
            <div class="product-gallery">
              <div class="product-main-image-wrap">
                <img
                  class="product-main-image"
                  :src="mainImage"
                  :alt="title"
                  role="button"
                  tabindex="0"
                  @click="openGalleryModal()"
                  @keydown.enter.prevent="openGalleryModal()"
                  @keydown.space.prevent="openGalleryModal()"
                >
                <button class="gallery-nav prev" type="button" @click="changeImage(-1)">‹</button>
                <button class="gallery-nav next" type="button" @click="changeImage(1)">›</button>
                <span class="image-count-badge large">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7a2 2 0 0 1 2-2h2l1.4-1.5h5.2L16 5h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z" /><path d="M12 16a3.2 3.2 0 1 0 0-6.4 3.2 3.2 0 0 0 0 6.4Z" /></svg>
                  {{ images.length }}
                </span>
              </div>
              <div class="product-thumbs">
                <button v-for="(image, index) in images" :key="image" type="button" :class="{ active: activeImage === index }" @click="activeImage = index">
                  <img :src="image" :alt="`${title} ${index + 1}`">
                </button>
              </div>
            </div>

            <aside class="bid-panel">
              <form class="bid-form" @submit.prevent="submitBid">
                <label>
                  <span>Ձեր հայտը</span>
                  <input v-model.number="bid.amount" type="number" min="100">
                </label>
                <button class="btn primary" type="submit" :disabled="bidPending">{{ bidPending ? 'Ուղարկվում է...' : t('btn.bid') }}</button>
              </form>
              <p>Ցանկանում եք գնել այս մեքենան հիմա առանց աճուրդի:</p>
              <NuxtLink class="btn primary full" :to="`/${lang}/contact`">{{ t('btn.buy') }} ({{ money(vehicle.buy_now) }})</NuxtLink>
              <p v-if="bidError" class="form-error">{{ bidError }}</p>
              <p v-if="bidSent" class="success-note">{{ t('form.success') }}</p>

              <h2>Աճուրդի տեղեկատվություն</h2>
              <div class="vehicle-specs">
                <div v-for="row in saleInfo" :key="row[0]" class="spec-row">
                  <span>{{ row[0] }}</span>
                  <strong>{{ row[1] }}</strong>
                </div>
              </div>
            </aside>
          </div>
        </div>

        <div class="product-lower-grid">
          <section class="details-panel">
            <div class="details-tabs" role="tablist" aria-label="Մեքենայի էջի բաժիններ">
              <button
                type="button"
                role="tab"
                :aria-selected="activeInfoTab === 'calculator'"
                :class="{ active: activeInfoTab === 'calculator' }"
                @click="activeInfoTab = 'calculator'"
              >
                Հաշվիչ
              </button>
              <button
                type="button"
                role="tab"
                :aria-selected="activeInfoTab === 'details'"
                :class="{ active: activeInfoTab === 'details' }"
                @click="activeInfoTab = 'details'"
              >
                Մեքենայի տվյալներ
              </button>
            </div>

            <div v-if="activeInfoTab === 'details'" class="details-columns" role="tabpanel">
              <div>
                <div v-for="row in detailsLeft" :key="row[0]" class="spec-row">
                  <span>{{ row[0] }}</span>
                  <strong>{{ row[1] }}</strong>
                </div>
              </div>
              <div>
                <div v-for="row in detailsRight" :key="row[0]" class="spec-row">
                  <span>{{ row[0] }}</span>
                  <strong>{{ row[1] }}</strong>
                </div>
              </div>
            </div>

            <form v-else class="vehicle-calculator-tab" role="tabpanel" @submit.prevent>
              <div class="vehicle-calc-grid">
                <label>
                  <span>Աճուրդում գնելու գումար</span>
                  <input v-model.number="calculatorForm.purchasePrice" type="number" min="0">
                </label>
                <label>
                  <span>Աճուրդի վճար</span>
                  <input v-model.number="calculatorForm.auctionFee" type="number" min="0">
                </label>
                <div class="field">
                  <span>Փոխադրման վայրը</span>
                  <AppSelect v-model="calculatorForm.transportFrom" :options="transportFromOptions" />
                </div>
                <div class="field">
                  <span>Նավահանգիստ / վայրը</span>
                  <AppSelect v-model="calculatorForm.transportTo" :options="transportToOptions" />
                </div>
              </div>

              <button class="vehicle-calc-submit" type="submit">Հաշվել</button>

              <div class="vehicle-calc-summary">
                <div v-for="row in calculatorRows" :key="row[0]" class="summary-row">
                  <span>{{ row[0] }}</span>
                  <strong>{{ row[1] }}</strong>
                </div>
                <button class="vehicle-calc-more" type="button">
                  <span>Լրացուցիչ ծախսեր</span>
                  <svg width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                    <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
                <div class="summary-row total">
                  <span>Ընդհանուր գին</span>
                  <strong>{{ money(calculatorTotal) }}</strong>
                </div>
              </div>

              <div class="customs-calculator">
                <h3>Մաքսազերծման վճար</h3>
                <p>Հաշվարկը մոտավոր է․ վերջնական գումարը կախված է մեքենայի փաստաթղթերից և փոխարժեքից։</p>

                <div class="customs-field-grid">
                  <div class="field">
                    <span>Աճուրդ</span>
                    <AppSelect v-model="calculatorForm.auction" :options="auctionOptions" />
                  </div>
                  <div class="field">
                    <span>Արտադրության տարի</span>
                    <AppSelect v-model="calculatorForm.year" :options="yearOptions" />
                  </div>
                  <div class="field">
                    <span>Շարժիչի տեսակ</span>
                    <AppSelect v-model="calculatorForm.engine" :options="engineOptions" />
                  </div>
                  <label>
                    <span>Շարժիչի ծավալ սմ³</span>
                    <input v-model.number="calculatorForm.engineSize" type="number" min="0">
                  </label>
                </div>

                <label class="vehicle-calc-check">
                  <input type="checkbox">
                  <span>ՌԴ-ից ներմուծվող մեքենա</span>
                </label>

                <div class="duty-card-grid">
                  <article v-for="card in dutyCards" :key="card.title" class="duty-card">
                    <h4>{{ card.title }}</h4>
                    <div v-for="row in card.rows" :key="`${card.title}-${row[0]}`" class="duty-row">
                      <span>{{ row[0] }}</span>
                      <strong>{{ row[1] }}</strong>
                    </div>
                    <div class="duty-row total">
                      <span>Ընդամենը</span>
                      <strong>{{ card.total }}</strong>
                    </div>
                  </article>
                </div>

                <button class="vehicle-calc-submit" type="submit">Հաշվել</button>
              </div>
            </form>
          </section>

          <aside class="callback-card">
            <h2>Ակտիվացրու ֆիլտրը նմանատիպ մեքենաների համար</h2>
            <form @submit.prevent="submitCallback">
              <div class="inline-fields">
                <label><span>Անուն</span><input v-model="callback.first_name" placeholder="Արմեն" required></label>
                <label><span>Ազգանուն</span><input v-model="callback.last_name" placeholder="Մկրտչյան"></label>
              </div>
              <label><span>Էլ. հասցե</span><input v-model="callback.email" type="email" placeholder="armen@mail.com" required></label>
              <button class="btn outline-red full" type="submit" :disabled="callbackPending">{{ callbackPending ? 'Ուղարկվում է...' : 'Ակտիվացնել' }}</button>
              <p v-if="callbackError" class="form-error">{{ callbackError }}</p>
              <p v-if="callbackSent" class="success-note">{{ t('form.success') }}</p>
            </form>
          </aside>
        </div>

        <section class="related-section">
          <h2>Մեքենաներ, որոնք ձեզ կարող են դուր գալ</h2>
          <div class="grid home-cars-grid">
            <VehicleCard v-for="item in related" :key="item.id" :vehicle="item" />
          </div>
        </section>
      </div>
    </section>

    <Teleport to="body">
      <div v-if="galleryModalOpen" class="gallery-modal" role="dialog" aria-modal="true" :aria-label="title" @click.self="closeGalleryModal">
        <button class="gallery-modal-close" type="button" @click="closeGalleryModal">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
          <span>Փակել</span>
        </button>

        <div class="gallery-modal-shell">
          <div class="gallery-modal-image-wrap">
            <img class="gallery-modal-image" :src="mainImage" :alt="title">
            <button class="gallery-modal-nav prev" type="button" aria-label="Նախորդ նկար" @click="changeImage(-1)">‹</button>
            <button class="gallery-modal-nav next" type="button" aria-label="Հաջորդ նկար" @click="changeImage(1)">›</button>
          </div>

          <div class="gallery-modal-thumbs" aria-label="Մեքենայի նկարներ">
            <button
              v-for="(image, index) in images"
              :key="`modal-${image}-${index}`"
              type="button"
              :class="{ active: activeImage === index }"
              @click="activeImage = index"
            >
              <img :src="image" :alt="`${title} ${index + 1}`">
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </main>
</template>
