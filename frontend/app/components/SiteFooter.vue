<script setup lang="ts">
const t = useT();
const lang = useLang();
const settings = useSiteSettings();
const footer = computed(() => settings.value.footer || {});

const footerAddress = computed(() => localize(settings.value.address || {}) || 'Երևան, Խանջյան 41');
const footerEmail = computed(() => settings.value.email || 'info@carmark.am');
const footerPhone = computed(() => settings.value.phone || '+374 968301');

const fallbackColumns = computed(() => [
  {
    title: t('nav.company'),
    links: [
      { label: 'Գլխավոր', to: '/' },
      { label: t('nav.about'), to: '/about' },
      { label: t('nav.services'), to: '/services' },
      { label: t('nav.how'), to: '/how-to-buy' },
    ],
  },
  {
    title: 'Մեքենաներ',
    links: [
      { label: 'Ավտոմեքենաներ', to: '/inventory' },
      { label: 'Մոտոցիկլետներ', to: '/inventory?vehicle=motorcycle' },
      { label: 'Բեռնատարներ', to: '/inventory?vehicle=truck' },
      { label: 'Ավտոբուսներ', to: '/inventory?vehicle=bus' },
    ],
  },
  {
    title: t('nav.auctions'),
    links: [
      { label: 'Ընթացիկ', to: '/auctions' },
      { label: 'Սպասվող', to: '/auctions?status=upcoming' },
      { label: t('nav.inventory'), to: '/inventory?private_sale=1' },
      { label: 'Գնել հիմա', to: '/inventory?buy_now=1' },
    ],
  },
  {
    title: 'Աջակցություն',
    links: [
      { label: t('nav.contact'), to: '/contact' },
      { label: t('nav.calculator'), to: '/calculator' },
      { label: 'FAQs', to: '/faqs' },
    ],
  },
]);

const columns = computed(() => footer.value.columns || fallbackColumns.value);

const socialLinks = computed(() => footer.value.social_links || [
  { label: 'Facebook', icon: 'facebook', href: '#' },
  { label: 'Instagram', icon: 'instagram', href: '#' },
  { label: 'Youtube', icon: 'youtube', href: '#' },
]);

const localizedTo = (to: string) => `/${lang.value}${to}`;
const footerLabel = (item: any) => item?.label_key ? t(item.label_key) : localize(item?.label) || item?.label || '';
const footerTitle = (column: any) => column?.title_key ? t(column.title_key) : localize(column?.title) || column?.title || '';
const copyrightText = computed(() => localize(footer.value.copyright) || t('footer.rights'));
</script>

<template>
  <footer class="site-footer">
    <div class="container footer-grid">
      <div class="footer-brand">
        <Logo :to="`/${lang}`" />
        <div class="footer-contact">
          <p>{{ footerAddress }}</p>
          <p>{{ footerEmail }}</p>
          <p>{{ footerPhone }}</p>
        </div>
      </div>

      <nav v-for="column in columns" :key="footerTitle(column)" class="footer-col" :aria-label="footerTitle(column)">
        <h3>{{ footerTitle(column) }}</h3>
        <NuxtLink v-for="item in column.links" :key="item.to" :to="localizedTo(item.to)">
          {{ footerLabel(item) }}
        </NuxtLink>
      </nav>

      <nav class="footer-col" aria-label="Հետևեք մեզ">
        <h3>Հետևեք մեզ</h3>
        <div class="footer-social">
          <a v-for="item in socialLinks" :key="item.label" class="footer-social-link" :href="item.href">
            <span class="footer-social-icon" aria-hidden="true">
              <svg v-if="item.icon === 'facebook'" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="8" r="7" fill="#101828" />
                <path d="M9.12 5.55H10.1V3.86C9.93 3.84 9.35 3.79 8.68 3.79C7.28 3.79 6.32 4.65 6.32 6.21V7.56H4.74V9.45H6.32V14H8.26V9.45H9.78L10.02 7.56H8.26V6.4C8.26 5.85 8.41 5.55 9.12 5.55Z" fill="white" />
              </svg>
              <svg v-else-if="item.icon === 'instagram'" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="8" r="7" fill="#101828" />
                <rect x="4.6" y="4.6" width="6.8" height="6.8" rx="1.8" stroke="white" stroke-width="1.2" />
                <circle cx="8" cy="8" r="1.55" stroke="white" stroke-width="1.2" />
                <circle cx="10.05" cy="5.95" r=".45" fill="white" />
              </svg>
              <svg v-else width="16" height="16" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="8" r="7" fill="#101828" />
                <path d="M11.68 6.16C11.59 5.8 11.31 5.52 10.96 5.43C10.32 5.25 7.8 5.25 7.8 5.25C7.8 5.25 5.28 5.25 4.64 5.43C4.29 5.52 4.01 5.8 3.92 6.16C3.75 6.81 3.75 8.16 3.75 8.16C3.75 8.16 3.75 9.51 3.92 10.16C4.01 10.52 4.29 10.8 4.64 10.89C5.28 11.07 7.8 11.07 7.8 11.07C7.8 11.07 10.32 11.07 10.96 10.89C11.31 10.8 11.59 10.52 11.68 10.16C11.85 9.51 11.85 8.16 11.85 8.16C11.85 8.16 11.85 6.81 11.68 6.16Z" fill="white" />
                <path d="M7.06 9.4L9.16 8.16L7.06 6.92V9.4Z" fill="#101828" />
              </svg>
            </span>
            {{ item.label }}
          </a>
        </div>
      </nav>
    </div>

    <div class="container footer-bottom">
      <span>@{{ footer.copyright_year || 2023 }} {{ copyrightText }}</span>
    </div>
  </footer>
</template>
