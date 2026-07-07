<script setup lang="ts">
const settings = reactive<any>({
  site_name: 'CarMark',
  phone: '',
  email: '',
  address: { hy: '', en: '', ru: '' },
});
const defaultAdvancedSettings = {
  navigation: {
    buy_link: { label_key: 'nav.buy', to: '/inventory' },
    inventory_link: { label_key: 'nav.inventory', to: '/inventory', query: { private_sale: '1' } },
    mobile_menu: [
      { label_key: 'nav.about', to: '/about' },
      { label_key: 'nav.services', to: '/services' },
      { label_key: 'nav.how', to: '/how-to-buy' },
      { label_key: 'nav.contact', to: '/contact' },
    ],
    auction_dropdown: [
      { label: { en: 'Current auctions' }, to: '/auctions', query: { tab: 'current' } },
      { label: { en: 'Upcoming auctions' }, to: '/auctions', query: { tab: 'upcoming' } },
      { label: { en: 'Copart auctions' }, to: '/inventory', query: { auction: 'Copart' } },
      { label: { en: 'IAAI auctions' }, to: '/inventory', query: { auction: 'IAAI' } },
    ],
    company_dropdown: [
      { label_key: 'nav.about', to: '/about' },
      { label_key: 'nav.services', to: '/services' },
      { label_key: 'nav.how', to: '/how-to-buy' },
      { label_key: 'nav.contact', to: '/contact' },
      { label: { en: 'FAQs' }, to: '/faqs' },
    ],
  },
  footer: {
    copyright_year: 2023,
    copyright: { en: 'All rights reserved' },
    social_links: [
      { label: 'Facebook', icon: 'facebook', href: '#' },
      { label: 'Instagram', icon: 'instagram', href: '#' },
      { label: 'Youtube', icon: 'youtube', href: '#' },
    ],
  },
  brand_logos: {},
  copy: {},
};
const notice = ref('');
const error = ref('');
const saving = ref(false);
const settingsJson = ref('{}');

async function loadSettings() {
  try {
    const response = await adminFetch('/admin/settings');
    const loaded = { ...defaultAdvancedSettings, ...(response.data || {}) };
    Object.assign(settings, loaded);
    settings.address = {
      hy: settings.address?.hy || '',
      en: settings.address?.en || '',
      ru: settings.address?.ru || '',
    };
    settingsJson.value = JSON.stringify(loaded, null, 2);
  } catch {
    await navigateTo('/admin/login');
  }
}

async function saveSettings() {
  saving.value = true;
  notice.value = '';
  error.value = '';

  try {
    let advanced = {};

    try {
      advanced = JSON.parse(settingsJson.value || '{}');
    } catch {
      error.value = 'Advanced settings JSON is invalid.';
      saving.value = false;
      return;
    }

    await adminFetch('/admin/settings', {
      method: 'PUT',
      body: {
        ...advanced,
        site_name: settings.site_name,
        phone: settings.phone,
        email: settings.email,
        address: settings.address,
      },
    });
    notice.value = 'Settings saved.';
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Unable to save settings.';
  } finally {
    saving.value = false;
  }
}

onMounted(loadSettings);
</script>

<template>
  <AdminShell title="Settings">
    <form class="form-panel admin-form" @submit.prevent="saveSettings">
      <label><span>Site name</span><input v-model="settings.site_name"></label>
      <label><span>Email</span><input v-model="settings.email" type="email"></label>
      <label><span>Phone</span><input v-model="settings.phone"></label>
      <label><span>Address EN</span><input v-model="settings.address.en"></label>
      <label><span>Address HY</span><input v-model="settings.address.hy"></label>
      <label><span>Address RU</span><input v-model="settings.address.ru"></label>
      <label>
        <span>Advanced site JSON</span>
        <textarea v-model="settingsJson" rows="18" spellcheck="false"></textarea>
      </label>
      <button class="btn primary" :disabled="saving">{{ saving ? 'Saving...' : 'Save settings' }}</button>
      <p v-if="error" class="form-error">{{ error }}</p>
      <p v-if="notice" class="success-note">{{ notice }}</p>
    </form>
  </AdminShell>
</template>
