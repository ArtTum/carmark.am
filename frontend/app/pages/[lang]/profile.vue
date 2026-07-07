<script setup lang="ts">
const route = useRoute();
const lang = useLang();
const { user, loggedIn, initAuth, updateProfile: updateAuthProfile, updatePassword: updateAuthPassword } = useAuth();
const watchedIds = ref<string[]>([]);
const savedSearches = ref<Array<{ id: number; label?: string; query?: Record<string, string> }>>([]);
const paymentNotice = ref('');
const personalNotice = ref('');
const personalError = ref('');
const passwordNotice = ref('');
const passwordError = ref('');
const { data } = await useFetch<any>(apiUrl('/vehicles'));

const activeTab = computed(() => String(route.query.tab || 'payment'));
const tabs = [
  ['transactions', 'Գործարքներ'],
  ['bids', 'Հայտեր'],
  ['personal', 'Անձնական տվյալներ'],
  ['payment', 'Վճարման եղանակ'],
  ['watchlist', 'Պահպանված մեքենաներ'],
  ['saved', 'Պահպանված որոնումներ'],
  ['password', 'Գաղտնաբառ'],
];
const countryOptions = ['Հայաստան', 'ԱՄՆ', 'Վրաստան', 'Ռուսաստան'];
const initialPaymentForm = {
  cardName: 'Olivia Rhye',
  expiry: '06 / 2028',
  cvv: '',
  cardNumber: '1234 1234 1234 1234',
  email: 'billing@untitledui.com',
  country: 'Հայաստան',
  city: 'Երևան',
  zip: '0007',
};
const paymentForm = reactive({ ...initialPaymentForm });
const initialPersonalForm = {
  firstName: 'Արմեն',
  lastName: 'Գասպարյան',
  email: 'armen@mail.com',
  phone: '+374 96 830001',
};
const personalForm = reactive({ ...initialPersonalForm });
const passwordForm = reactive({ current: '', next: '', confirm: '' });

const fallbackVehicles = [
  {
    id: 'profile-1',
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
    id: 'profile-2',
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
];

const vehicles = computed(() => data.value?.data?.length ? data.value.data : fallbackVehicles);
const watched = computed(() => {
  if (!watchedIds.value.length) return [];
  return vehicles.value.filter((vehicle: any) => watchedIds.value.includes(String(vehicle.id)));
});

function readStoredArray(key: string) {
  if (!import.meta.client) return [];

  try {
    const value = JSON.parse(localStorage.getItem(key) || '[]');
    return Array.isArray(value) ? value : [];
  } catch {
    localStorage.removeItem(key);
    return [];
  }
}

function savedSearchTo(search: { query?: Record<string, string> }) {
  return { path: `/${lang.value}/inventory`, query: search.query || {} };
}

function savedSearchSummary(search: { query?: Record<string, string>; label?: string }) {
  if (search.label) return search.label;
  const values = Object.values(search.query || {}).filter(Boolean);
  return values.length ? values.join(', ') : 'Բոլոր մեքենաները';
}

function removeSavedSearch(id: number) {
  savedSearches.value = savedSearches.value.filter((search) => search.id !== id);
  localStorage.setItem('savedSearches', JSON.stringify(savedSearches.value));
}

function showNotice(target: { value: string }, text: string) {
  target.value = text;
  window.setTimeout(() => {
    target.value = '';
  }, 2200);
}

function resetPaymentForm() {
  Object.assign(paymentForm, initialPaymentForm);
  paymentNotice.value = '';
}

function savePaymentForm() {
  showNotice(paymentNotice, 'Վճարման տվյալները պահպանվեցին։');
}

function syncUserForm() {
  if (!user.value) return;
  const [firstName, ...rest] = user.value.name.split(' ');
  personalForm.firstName = firstName || user.value.name;
  personalForm.lastName = rest.join(' ');
  personalForm.email = user.value.email;
}

async function savePersonalForm() {
  personalError.value = '';
  personalNotice.value = '';

  try {
    await updateAuthProfile({
      name: `${personalForm.firstName} ${personalForm.lastName}`.trim(),
      email: personalForm.email,
    });
    syncUserForm();
    showNotice(personalNotice, 'Անձնական տվյալները պահպանվեցին։');
  } catch (exception: any) {
    personalError.value = exception?.data?.message || 'Տվյալները չհաջողվեց պահպանել։';
  }
}

async function updatePassword() {
  passwordError.value = '';
  passwordNotice.value = '';

  if (!passwordForm.current || !passwordForm.next || !passwordForm.confirm) {
    passwordError.value = 'Լրացրեք բոլոր դաշտերը։';
    return;
  }

  if (passwordForm.next !== passwordForm.confirm) {
    passwordError.value = 'Նոր գաղտնաբառերը չեն համընկնում։';
    return;
  }

  try {
    await updateAuthPassword({
      current_password: passwordForm.current,
      password: passwordForm.next,
      password_confirmation: passwordForm.confirm,
    });
    Object.assign(passwordForm, { current: '', next: '', confirm: '' });
    showNotice(passwordNotice, 'Գաղտնաբառը թարմացվեց։');
  } catch (exception: any) {
    passwordError.value = exception?.data?.message || 'Գաղտնաբառը չհաջողվեց թարմացնել։';
  }
}

onMounted(async () => {
  await initAuth();
  if (!loggedIn.value) {
    await navigateTo({
      path: `/${lang.value}/login`,
      query: { redirect: route.fullPath },
    });
    return;
  }

  syncUserForm();
  watchedIds.value = readStoredArray('watchlist').map(String);
  savedSearches.value = readStoredArray('savedSearches');
});
</script>

<template>
  <main class="profile-page">
    <section class="section profile-shell">
      <div class="container profile-layout">
        <aside class="profile-sidebar">
          <NuxtLink
            v-for="tab in tabs"
            :key="tab[0]"
            :to="{ path: `/${lang}/profile`, query: { tab: tab[0] } }"
            :class="{ active: activeTab === tab[0] }"
          >
            {{ tab[1] }}
          </NuxtLink>
        </aside>

        <section class="profile-content">
          <template v-if="activeTab === 'payment'">
            <h1>Վճարման եղանակ</h1>
            <p>Թարմացրեք ձեր վճարման տվյալները և հասցեն։</p>
            <form class="profile-card payment-form" @submit.prevent="savePaymentForm">
              <label><span>Անունը քարտի վրա</span><input v-model="paymentForm.cardName"></label>
              <div class="payment-grid">
                <label><span>Expiry</span><input v-model="paymentForm.expiry"></label>
                <label><span>CVV</span><input v-model="paymentForm.cvv" placeholder="•••"></label>
              </div>
              <label><span>Քարտի համար</span><input v-model="paymentForm.cardNumber"></label>
              <label><span>Էլեկտրոնային հասցե</span><input v-model="paymentForm.email" type="email"></label>
              <div class="field"><span>Երկիր</span><AppSelect v-model="paymentForm.country" :options="countryOptions" /></div>
              <div class="payment-grid">
                <label><span>Քաղաք</span><input v-model="paymentForm.city"></label>
                <label><span>ZIP</span><input v-model="paymentForm.zip"></label>
              </div>
              <p v-if="paymentNotice" class="success-note">{{ paymentNotice }}</p>
              <div class="profile-actions">
                <button class="btn ghost" type="button" @click="resetPaymentForm">Չեղարկել</button>
                <button class="btn primary" type="submit">Պահպանել տվյալները</button>
              </div>
            </form>
          </template>

          <template v-else-if="activeTab === 'personal'">
            <h1>Անձնական տվյալներ</h1>
            <form class="profile-card payment-form" @submit.prevent="savePersonalForm">
              <div class="payment-grid">
                <label><span>Անուն</span><input v-model="personalForm.firstName"></label>
                <label><span>Ազգանուն</span><input v-model="personalForm.lastName"></label>
              </div>
              <label><span>Էլ. հասցե</span><input v-model="personalForm.email" type="email"></label>
              <label><span>Հեռախոս</span><input v-model="personalForm.phone"></label>
              <p v-if="personalError" class="form-error">{{ personalError }}</p>
              <p v-if="personalNotice" class="success-note">{{ personalNotice }}</p>
              <button class="btn primary" type="submit">Պահպանել</button>
            </form>
          </template>

          <template v-else-if="activeTab === 'password'">
            <h1>Փոխել գաղտնաբառը</h1>
            <form class="profile-card payment-form compact-form" @submit.prevent="updatePassword">
              <label><span>Ընթացիկ գաղտնաբառ</span><input v-model="passwordForm.current" type="password"></label>
              <label><span>Նոր գաղտնաբառ</span><input v-model="passwordForm.next" type="password"></label>
              <label><span>Կրկնել գաղտնաբառը</span><input v-model="passwordForm.confirm" type="password"></label>
              <p v-if="passwordError" class="form-error">{{ passwordError }}</p>
              <p v-if="passwordNotice" class="success-note">{{ passwordNotice }}</p>
              <button class="btn primary" type="submit">Թարմացնել</button>
            </form>
          </template>

          <template v-else-if="activeTab === 'watchlist'">
            <h1>Պահպանված մեքենաներ</h1>
            <div v-if="watched.length" class="grid home-cars-grid">
              <VehicleCard v-for="vehicle in watched" :key="vehicle.id" :vehicle="vehicle" />
            </div>
            <div v-else class="profile-card saved-search-card">
              <strong>Պահպանված մեքենա չկա</strong>
              <span>Մեքենաների քարտերից սեղմեք սրտիկը, և դրանք կհայտնվեն այստեղ։</span>
              <NuxtLink class="btn ghost small" :to="`/${lang}/inventory`">Բացել մեքենաները</NuxtLink>
            </div>
          </template>

          <template v-else-if="activeTab === 'saved'">
            <h1>Պահպանված որոնումներ</h1>
            <div v-if="savedSearches.length" class="saved-search-list">
              <article v-for="search in savedSearches" :key="search.id" class="profile-card saved-search-card">
                <strong>Պահպանված որոնում</strong>
                <span>{{ savedSearchSummary(search) }}</span>
                <div class="saved-search-actions">
                  <NuxtLink class="btn ghost small" :to="savedSearchTo(search)">Բացել</NuxtLink>
                  <button class="btn ghost small" type="button" @click="removeSavedSearch(search.id)">Ջնջել</button>
                </div>
              </article>
            </div>
            <div v-else class="profile-card saved-search-card">
              <strong>Պահպանված որոնում չկա</strong>
              <span>Inventory էջում ընտրեք ֆիլտրերը և սեղմեք «Պահպանել որոնումը»։</span>
              <NuxtLink class="btn ghost small" :to="`/${lang}/inventory`">Բացել որոնումը</NuxtLink>
            </div>
          </template>

          <template v-else-if="activeTab === 'bids'">
            <h1>Իմ հայտերը</h1>
            <div class="profile-table">
              <div><strong>2022 Dodge Charger GT</strong><span>{{ money(8700) }}</span><em>Սպասում է հաստատման</em></div>
              <div><strong>2024 BMW M2</strong><span>{{ money(5025) }}</span><em>Ակտիվ</em></div>
            </div>
          </template>

          <template v-else>
            <h1>Գործարքներ</h1>
            <div class="profile-table">
              <div><strong>#CM-2481</strong><span>Դեպոզիտ</span><em>{{ money(500) }}</em></div>
              <div><strong>#CM-2482</strong><span>Առաքման վճար</span><em>{{ money(1600) }}</em></div>
            </div>
          </template>
        </section>
      </div>
    </section>
  </main>
</template>
