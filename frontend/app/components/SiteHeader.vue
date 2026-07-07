<script setup lang="ts">
const t = useT();
const lang = useLang();
const route = useRoute();
const query = ref('');
const accountOpen = ref(false);
const langOpen = ref(false);
const menuOpen = ref(false);
const headerRef = ref<HTMLElement | null>(null);

const langLabels: Record<string, string> = {
  hy: 'Հայերեն',
  en: 'English',
  ru: 'Русский',
};

const switchLang = (code: string) => {
  const parts = route.path.split('/').filter(Boolean);
  if (['hy', 'en', 'ru'].includes(parts[0])) parts[0] = code;
  else parts.unshift(code);
  return `/${parts.join('/')}`;
};

function submit() {
  return navigateTo({ path: `/${lang.value}/inventory`, query: query.value ? { q: query.value } : {} });
}

function toggleAccount() {
  accountOpen.value = !accountOpen.value;
  menuOpen.value = false;
  langOpen.value = false;
}

function toggleMenu() {
  menuOpen.value = !menuOpen.value;
  accountOpen.value = false;
  langOpen.value = false;
}

function toggleLang() {
  langOpen.value = !langOpen.value;
  accountOpen.value = false;
  menuOpen.value = false;
}

function closeHeaderMenus() {
  accountOpen.value = false;
  langOpen.value = false;
  menuOpen.value = false;
}

function handleDocumentClick(event: MouseEvent) {
  if (!headerRef.value?.contains(event.target as Node)) {
    closeHeaderMenus();
  }
}

function handleKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape') {
    closeHeaderMenus();
  }
}

onMounted(() => {
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
          <button class="search-submit" type="submit" aria-label="Search">
            <svg width="44" height="44" viewBox="0 0 44 44" fill="none" aria-hidden="true">
              <rect width="44" height="44" rx="22" fill="#ED1C24" />
              <path d="M31 31L26.65 26.65M29 21C29 25.4183 25.4183 29 21 29C16.5817 29 13 25.4183 13 21C13 16.5817 16.5817 13 21 13C25.4183 13 29 16.5817 29 21Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </form>

        <div class="quick-tools">
          <div class="account-wrap">
            <button class="account-pill" type="button" :aria-expanded="accountOpen" @click="toggleAccount">
              <span class="red-action-icon">
                <svg width="44" height="44" viewBox="0 0 44 44" fill="none" aria-hidden="true">
                  <rect width="44" height="44" rx="22" fill="#ED1C24" />
                  <path d="M27.5 16.5L16.5 27.5M18.5 20.5V16.5M16.5 18.5H20.5M23.5 25.5H27.5M17.8 31H26.2C27.8802 31 28.7202 31 29.362 30.673C29.9265 30.3854 30.3854 29.9265 30.673 29.362C31 28.7202 31 27.8802 31 26.2V17.8C31 16.1198 31 15.2798 30.673 14.638C30.3854 14.0735 29.9265 13.6146 29.362 13.327C28.7202 13 27.8802 13 26.2 13H17.8C16.1198 13 15.2798 13 14.638 13.327C14.0735 13.6146 13.6146 14.0735 13.327 14.638C13 15.2798 13 16.1198 13 17.8V26.2C13 27.8802 13 28.7202 13.327 29.362C13.6146 29.9265 14.0735 30.3854 14.638 30.673C15.2798 31 16.1198 31 17.8 31Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </span>
              <span>Հաշիվ</span>
              <svg class="chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
            <div v-if="accountOpen" class="account-menu">
              <div class="account-menu-head">
                <strong>Արմեն Գասպարյան</strong>
                <span>ID 55466458841453486</span>
              </div>

              <nav class="account-menu-section">
                <NuxtLink :to="`/${lang}/profile`">Գործարքներ</NuxtLink>
                <NuxtLink :to="`/${lang}/profile`">Հայտեր</NuxtLink>
                <NuxtLink class="is-active" :to="`/${lang}/profile`">Վճարման եղանակ</NuxtLink>
              </nav>

              <nav class="account-menu-section">
                <NuxtLink :to="`/${lang}/profile`">Պահպանված մեքենաներ</NuxtLink>
                <NuxtLink :to="`/${lang}/profile`">Պահպանված որոնումներ</NuxtLink>
              </nav>

              <button class="account-logout" type="button">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M14 8V5.5A1.5 1.5 0 0 0 12.5 4h-7A1.5 1.5 0 0 0 4 5.5v13A1.5 1.5 0 0 0 5.5 20h7a1.5 1.5 0 0 0 1.5-1.5V16" />
                  <path d="M10 12h10M17 9l3 3-3 3" />
                </svg>
                <span>Դուրս գալ</span>
              </button>
            </div>
          </div>

          <NuxtLink class="round-action" :to="`/${lang}/calculator`" aria-label="Calculator">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" aria-hidden="true">
              <rect x="0.5" y="0.5" width="47" height="47" rx="23.5" stroke="#D0D5DD" />
              <path d="M32 21.5V19.2C32 18.0799 32 17.5198 31.782 17.092C31.5903 16.7157 31.2843 16.4097 30.908 16.218C30.4802 16 29.9201 16 28.8 16H17.2C16.0799 16 15.5198 16 15.092 16.218C14.7157 16.4097 14.4097 16.7157 14.218 17.092C14 17.5198 14 18.0799 14 19.2V28.8C14 29.9201 14 30.4802 14.218 30.908C14.4097 31.2843 14.7157 31.5903 15.092 31.782C15.5198 32 16.0799 32 17.2 32L28.8 32C29.9201 32 30.4802 32 30.908 31.782C31.2843 31.5903 31.5903 31.2843 31.782 30.908C32 30.4802 32 29.9201 32 28.8V26.5M27 24C27 23.5353 27 23.303 27.0384 23.1098C27.1962 22.3164 27.8164 21.6962 28.6098 21.5384C28.803 21.5 29.0353 21.5 29.5 21.5H31.5C31.9647 21.5 32.197 21.5 32.3902 21.5384C33.1836 21.6962 33.8038 22.3164 33.9616 23.1098C34 23.303 34 23.5353 34 24C34 24.4647 34 24.697 33.9616 24.8902C33.8038 25.6836 33.1836 26.3038 32.3902 26.4616C32.197 26.5 31.9647 26.5 31.5 26.5H29.5C29.0353 26.5 28.803 26.5 28.6098 26.4616C27.8164 26.3038 27.1962 25.6836 27.0384 24.8902C27 24.697 27 24.4647 27 24Z" stroke="#101828" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </NuxtLink>

          <div class="menu-wrap">
            <button class="menu-pill" type="button" aria-label="Menu" :aria-expanded="menuOpen" @click="toggleMenu">
              <span class="hamburger-lines" aria-hidden="true">
                <span />
                <span />
                <span />
              </span>
              <span class="menu-dot" aria-hidden="true" />
            </button>

            <nav v-if="menuOpen" class="main-menu-popover">
              <NuxtLink class="is-active" :to="`/${lang}/about`" @click="menuOpen = false">Մեր մասին</NuxtLink>
              <NuxtLink :to="`/${lang}/services`" @click="menuOpen = false">Ծառայություններ</NuxtLink>
              <NuxtLink :to="`/${lang}/how-to-buy`" @click="menuOpen = false">Ինչպես գնել</NuxtLink>
              <NuxtLink :to="`/${lang}/contact`" @click="menuOpen = false">Կապ մեզ հետ</NuxtLink>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <div class="header-nav-line">
      <div class="container header-nav-inner">
        <nav class="main-nav">
          <NuxtLink :to="`/${lang}/inventory`">Գնել Մեքենա</NuxtLink>
          <NuxtLink class="nav-with-chevron" :to="`/${lang}/auctions`">
            Աճուրդներ
            <svg class="chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
              <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </NuxtLink>
          <NuxtLink class="nav-with-chevron" :to="`/${lang}/services`">
            Ընկերություն
            <svg class="chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
              <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </NuxtLink>
          <NuxtLink :to="{ path: `/${lang}/inventory`, query: { private_sale: 1 } }">Առկա տեսականի</NuxtLink>
        </nav>

        <div class="language-wrap">
          <button class="language-button" type="button" @click="toggleLang">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
              <path d="M3.6 9h16.8M3.6 15h16.8M12 3c2.2 2.3 3.3 5.3 3.3 9S14.2 18.7 12 21M12 3C9.8 5.3 8.7 8.3 8.7 12S9.8 18.7 12 21" />
            </svg>
            <span>{{ langLabels[lang] }}</span>
            <svg class="chevron" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
              <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="#101828" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
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
