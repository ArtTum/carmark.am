<script setup lang="ts">
const settings = reactive<any>({
  site_name: 'CarMark',
  phone: '',
  email: '',
  address: { hy: '', en: '', ru: '' },
});
const notice = ref('');

async function loadSettings() {
  try {
    const response = await adminFetch('/admin/settings');
    Object.assign(settings, response.data || {});
  } catch {
    await navigateTo('/admin/login');
  }
}

async function saveSettings() {
  await adminFetch('/admin/settings', {
    method: 'PUT',
    body: settings,
  });
  notice.value = 'Settings saved.';
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
      <button class="btn primary">Save settings</button>
      <p v-if="notice" class="success-note">{{ notice }}</p>
    </form>
  </AdminShell>
</template>
