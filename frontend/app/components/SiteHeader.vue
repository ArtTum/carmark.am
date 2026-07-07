<script setup lang="ts">
const t = useT();
const lang = useLang();
const route = useRoute();
const settings = useSiteSettings();
const { user, loggedIn, pending: authPending, initAuth, login, register, logout } = useAuth();

const query = ref(String(route.query.q || ''));
const accountOpen = ref(false);
const langOpen = ref(false);
const menuOpen = ref(false);
const navDropdown = ref<'auctions' | 'company' | null>(null);
const headerRef = ref<HTMLElement | null>(null);
const authMode = ref<'login' | 'register'>('login');
const authError = ref('');
const authForm = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const langLabels: Record<string, string> = {
  hy: 'Հայերեն',
  en: 'English',
  ru: 'Русский',
};

const profileLinks = [
  { label: 'Գործարքներ', query: 'transactions' },
  { label: 'Հայտեր', query: 'bids' },
  { label: 'Վճարման եղանակ', query: 'payment' },
  { label: 'Պահպանված մեքենաներ', query: 'watchlist' },
  { label: 'Պահպանված որոնումներ', query: 'saved' },
];
const primaryProfileLinks = profileLinks.slice(0, 3);
const savedProfileLinks = profileLinks.slice(3);
const accountName = computed(() => user.value?.name || 'Հյուր');
const accountId = computed(() => user.value?.id ? `ID ${user.value.id}` : '');

const fallbackMenuLinks = [
  { label: t('nav.about'), to: '/about' },
  { label: t('nav.services'), to: '/services' },
  { label: t('nav.how'), to: '/how-to-buy' },
  { label: t('nav.contact'), to: '/contact' },
];
const fallbackAuctionDropdownLinks = [
  { label: 'Ընթացիկ աճուրդներ', to: '/auctions', query: { tab: 'current' } },
  { label: 'Սպասվող աճուրդներ', to: '/auctions', query: { tab: 'upcoming' } },
  { label: 'Copart աճուրդներ', to: '/inventory', query: { auction: 'Copart' } },
  { label: 'IAAI աճուրդներ', to: '/inventory', query: { auction: 'IAAI' } },
];
const fallbackCompanyDropdownLinks = [
  { label: t('nav.about'), to: '/about' },
  { label: t('nav.services'), to: '/services' },
  { label: t('nav.how'), to: '/how-to-buy' },
  { label: t('nav.contact'), to: '/contact' },
  { label: 'FAQs', to: '/faqs' },
];

const navigation = computed(() => settings.value.navigation || {});
const menuLinks = computed(() => navigation.value.mobile_menu || fallbackMenuLinks);
const auctionDropdownLinks = computed(() => navigation.value.auction_dropdown || fallbackAuctionDropdownLinks);
const companyDropdownLinks = computed(() => navigation.value.company_dropdown || fallbackCompanyDropdownLinks);
const buyLink = computed(() => navigation.value.buy_link || { label_key: 'nav.buy', to: '/inventory' });
const mainInventoryLink = computed(() => navigation.value.inventory_link || { label_key: 'nav.inventory', to: '/inventory', query: { private_sale: 1 } });

const switchLang = (code: string) => {
  const parts = route.path.split('/').filter(Boolean);
  if (['hy', 'en', 'ru'].includes(parts[0])) parts[0] = code;
  else parts.unshift(code);
  return { path: `/${parts.join('/')}`, query: route.query };
};

function localized(path: string) {
  return `/${lang.value}${path}`;
}

function submit() {
  closeHeaderMenus();
  return navigateTo({ path: `/${lang.value}/inventory`, query: query.value ? { q: query.value } : {} });
}

watch(() => route.query.q, (value) => {
  query.value = String(value || '');
});

function closeHeaderMenus() {
  accountOpen.value = false;
  langOpen.value = false;
  menuOpen.value = false;
  navDropdown.value = null;
}

function toggleAccount() {
  accountOpen.value = !accountOpen.value;
  langOpen.value = false;
  menuOpen.value = false;
  navDropdown.value = null;
  authError.value = '';
}

function toggleLang() {
  langOpen.value = !langOpen.value;
  accountOpen.value = false;
  menuOpen.value = false;
  navDropdown.value = null;
}

function toggleMenu() {
  menuOpen.value = !menuOpen.value;
  accountOpen.value = false;
  langOpen.value = false;
  navDropdown.value = null;
}

function toggleNavDropdown(name: 'auctions' | 'company') {
  navDropdown.value = navDropdown.value === name ? null : name;
  accountOpen.value = false;
  langOpen.value = false;
  menuOpen.value = false;
}

function dropdownTo(item: any) {
  return { path: localized(item?.to || '/'), query: item?.query || {} };
}

function isProfileLinkActive(queryName: string) {
  if (route.path.includes('/profile')) return String(route.query.tab || 'payment') === queryName;
  return queryName === 'payment';
}

function navLabel(item: any) {
  return item?.label_key ? t(item.label_key) : localize(item?.label) || item?.label || '';
}

async function submitAuth() {
  authError.value = '';

  try {
    if (authMode.value === 'login') {
      await login(authForm.email, authForm.password);
    } else {
      await register({
        name: authForm.name,
        email: authForm.email,
        password: authForm.password,
        password_confirmation: authForm.password_confirmation,
      });
    }

    Object.assign(authForm, { name: '', email: '', password: '', password_confirmation: '' });
    accountOpen.value = false;
  } catch (exception: any) {
    authError.value = exception?.data?.message || 'Չհաջողվեց մուտք գործել։ Ստուգեք տվյալները։';
  }
}

async function logoutAccount() {
  await logout();
  closeHeaderMenus();
  if (route.path.includes('/profile')) return navigateTo(localized('/'));
}

function handleDocumentClick(event: MouseEvent) {
  if (!headerRef.value?.contains(event.target as Node)) closeHeaderMenus();
}

function handleKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape') closeHeaderMenus();
}

onMounted(() => {
  initAuth();
  document.addEventListener('click', handleDocumentClick);
  document.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleDocumentClick);
  document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
  <header ref="headerRef" class="site-header">
    <div class="header-top">
      <div class="container header-top-inner">
        <Logo :to="`/${lang}`" />

        <form class="top-search" @submit.prevent="submit">
          <input v-model="query" type="search" :placeholder="t('search.placeholder')">
          <button class="search-submit" type="submit" :aria-label="t('btn.search')">
            <svg width="44" height="44" viewBox="0 0 44 44" fill="none" aria-hidden="true">
              <rect width="44" height="44" rx="22" fill="#ED1C24" />
              <path d="M31 31L26.65 26.65M29 21C29 25.4183 25.4183 29 21 29C16.5817 29 13 25.4183 13 21C13 16.5817 16.5817 13 21 13C25.4183 13 29 16.5817 29 21Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </form>

        <div class="quick-tools">
          <NuxtLink class="account-pill calc-pill" :to="localized('/calculator')" :aria-label="t('nav.calculator')">
            <span class="red-action-icon">
              <svg width="44" height="44" viewBox="0 0 44 44" fill="none" aria-hidden="true">
                <rect width="44" height="44" rx="22" fill="#ED1C24" />
                <path d="M27.5 16.5L16.5 27.5M18.5 20.5V16.5M16.5 18.5H20.5M23.5 25.5H27.5M17.8 31H26.2C27.8802 31 28.7202 31 29.362 30.673C29.9265 30.3854 30.3854 29.9265 30.673 29.362C31 28.7202 31 27.8802 31 26.2V17.8C31 16.1198 31 15.2798 30.673 14.638C30.3854 14.0735 29.9265 13.6146 29.362 13.327C28.7202 13 27.8802 13 26.2 13H17.8C16.1198 13 15.2798 13 14.638 13.327C14.0735 13.6146 13.6146 14.0735 13.327 14.638C13 15.2798 13 16.1198 13 17.8V26.2C13 27.8802 13 28.7202 13.327 29.362C13.6146 29.9265 14.0735 30.3854 14.638 30.673Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </span>
            <span>{{ t('nav.calculator') }}</span>
          </NuxtLink>

          <div class="account-wrap">
            <button class="round-action" type="button" :aria-label="t('nav.account')" :aria-expanded="accountOpen" @click="toggleAccount">
              <svg width="48" height="48" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                <rect x="0.5" y="0.5" width="47" height="47" rx="23.5" stroke="#D0D5DD" />
                <path d="M32 21.5V19.2C32 18.0799 32 17.5198 31.782 17.092C31.5903 16.7157 31.2843 16.4097 30.908 16.218C30.4802 16 29.9201 16 28.8 16H17.2C16.0799 16 15.5198 16 15.092 16.218C14.7157 16.4097 14.4097 16.7157 14.218 17.092C14 17.5198 14 18.0799 14 19.2V28.8C14 29.9201 14 30.4802 14.218 30.908C14.4097 31.2843 14.7157 31.5903 15.092 31.782C15.5198 32 16.0799 32 17.2 32H28.8C29.9201 32 30.4802 32 30.908 31.782C31.2843 31.5903 31.5903 31.2843 31.782 30.908C32 30.4802 32 29.9201 32 28.8V26.5M27 24C27 22.8954 27.8954 22 29 22H32C33.1046 22 34 22.8954 34 24C34 25.1046 33.1046 26 32 26H29C27.8954 26 27 25.1046 27 24Z" stroke="#101828" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>

            <div v-if="accountOpen" class="account-menu">
              <div v-if="loggedIn" class="account-menu-head">
                <strong>{{ accountName }}</strong>
                <span>{{ accountId }}</span>
              </div>

              <nav v-if="loggedIn" class="account-menu-section">
                <NuxtLink
                  v-for="item in primaryProfileLinks"
                  :key="item.query"
                  :class="{ 'is-active': isProfileLinkActive(item.query) }"
                  :to="{ path: localized('/profile'), query: { tab: item.query } }"
                  @click="accountOpen = false"
                >
                  {{ item.label }}
                </NuxtLink>
              </nav>

              <nav v-if="loggedIn" class="account-menu-section">
                <NuxtLink
                  v-for="item in savedProfileLinks"
                  :key="item.query"
                  :class="{ 'is-active': isProfileLinkActive(item.query) }"
                  :to="{ path: localized('/profile'), query: { tab: item.query } }"
                  @click="accountOpen = false"
                >
                  {{ item.label }}
                </NuxtLink>
              </nav>

              <button v-if="loggedIn" class="account-logout" type="button" @click="logoutAccount">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M14 8V5.5A1.5 1.5 0 0 0 12.5 4h-7A1.5 1.5 0 0 0 4 5.5v13A1.5 1.5 0 0 0 5.5 20h7a1.5 1.5 0 0 0 1.5-1.5V16" />
                  <path d="M10 12h10M17 9l3 3-3 3" />
                </svg>
                <span>Դուրս գալ</span>
              </button>

              <form v-if="!loggedIn" class="auth-menu-form" @submit.prevent="submitAuth">
                <div class="auth-tabs" role="tablist" aria-label="Մուտք">
                  <button type="button" :class="{ active: authMode === 'login' }" @click="authMode = 'login'; authError = ''">Մուտք</button>
                  <button type="button" :class="{ active: authMode === 'register' }" @click="authMode = 'register'; authError = ''">Գրանցվել</button>
                </div>

                <label v-if="authMode === 'register'">
                  <span>Անուն</span>
                  <input v-model="authForm.name" autocomplete="name" required>
                </label>
                <label>
                  <span>Էլ. հասցե</span>
                  <input v-model="authForm.email" type="email" autocomplete="email" required>
                </label>
                <label>
                  <span>Գաղտնաբառ</span>
                  <input v-model="authForm.password" type="password" :autocomplete="authMode === 'login' ? 'current-password' : 'new-password'" required minlength="6">
                </label>
                <label v-if="authMode === 'register'">
                  <span>Կրկնել գաղտնաբառը</span>
                  <input v-model="authForm.password_confirmation" type="password" autocomplete="new-password" required minlength="6">
                </label>

                <p v-if="authError" class="auth-error">{{ authError }}</p>
                <button class="auth-submit" type="submit" :disabled="authPending">
                  {{ authPending ? 'Խնդրում ենք սպասել...' : (authMode === 'login' ? 'Մուտք գործել' : 'Ստեղծել հաշիվ') }}
                </button>
                <div class="auth-menu-links">
                  <NuxtLink :to="localized('/login')" @click="accountOpen = false">Մուտքի էջ</NuxtLink>
                  <NuxtLink :to="localized('/register')" @click="accountOpen = false">Գրանցման էջ</NuxtLink>
                </div>
              </form>
            </div>
          </div>

          <div class="menu-wrap">
            <button class="menu-pill" type="button" :aria-label="t('nav.company')" :aria-expanded="menuOpen" @click="toggleMenu">
              <span class="hamburger-lines" aria-hidden="true"><span /><span /><span /></span>
              <span class="menu-dot" aria-hidden="true" />
            </button>

            <nav v-if="menuOpen" class="main-menu-popover">
              <NuxtLink v-for="item in menuLinks" :key="item.to" :to="localized(item.to)" @click="menuOpen = false">
                {{ navLabel(item) }}
              </NuxtLink>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <div class="header-nav-line">
      <div class="container header-nav-inner">
        <nav class="main-nav">
          <NuxtLink :to="dropdownTo(buyLink)">{{ navLabel(buyLink) }}</NuxtLink>
          <div class="nav-dropdown">
            <button
              class="nav-with-chevron nav-dropdown-trigger"
              type="button"
              :class="{ open: navDropdown === 'auctions' }"
              :aria-expanded="navDropdown === 'auctions'"
              @click="toggleNavDropdown('auctions')"
            >
              {{ t('nav.auctions') }}
              <svg class="chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
            <nav v-if="navDropdown === 'auctions'" class="nav-dropdown-menu">
              <NuxtLink
                v-for="item in auctionDropdownLinks"
                :key="`${item.to}-${navLabel(item)}`"
                :to="dropdownTo(item)"
                @click="navDropdown = null"
              >
                {{ navLabel(item) }}
              </NuxtLink>
            </nav>
          </div>
          <div class="nav-dropdown">
            <button
              class="nav-with-chevron nav-dropdown-trigger"
              type="button"
              :class="{ open: navDropdown === 'company' }"
              :aria-expanded="navDropdown === 'company'"
              @click="toggleNavDropdown('company')"
            >
              {{ t('nav.company') }}
              <svg class="chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
            <nav v-if="navDropdown === 'company'" class="nav-dropdown-menu">
              <NuxtLink
                v-for="item in companyDropdownLinks"
                :key="`${item.to}-${navLabel(item)}`"
                :to="dropdownTo(item)"
                @click="navDropdown = null"
              >
                {{ navLabel(item) }}
              </NuxtLink>
            </nav>
          </div>
          <NuxtLink :to="dropdownTo(mainInventoryLink)">{{ navLabel(mainInventoryLink) }}</NuxtLink>
        </nav>

        <div class="language-wrap">
          <button class="language-button" type="button" :aria-expanded="langOpen" @click="toggleLang">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
              <path d="M3.6 9h16.8M3.6 15h16.8M12 3c2.2 2.3 3.3 5.3 3.3 9S14.2 18.7 12 21M12 3C9.8 5.3 8.7 8.3 8.7 12S9.8 18.7 12 21" />
            </svg>
            <span>{{ langLabels[lang] }}</span>
            <svg class="chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
              <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <div v-if="langOpen" class="language-menu">
            <NuxtLink v-for="code in ['hy', 'en', 'ru']" :key="code" :to="switchLang(code)" @click="langOpen = false">
              {{ langLabels[code] }}
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
