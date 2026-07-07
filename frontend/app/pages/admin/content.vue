<script setup lang="ts">
const pages = ref<any[]>([]);
const active = ref<any>(null);
const bodyJson = ref('{}');
const notice = ref('');
const error = ref('');
const saving = ref(false);

async function loadPages() {
  try {
    const response = await adminFetch('/admin/pages');
    pages.value = response.data || [];
    if (!active.value && pages.value[0]) editPage(pages.value[0]);
  } catch {
    await navigateTo('/admin/login');
  }
}

function editPage(page: any) {
  active.value = JSON.parse(JSON.stringify(page));
  active.value.title ||= { en: '', hy: '', ru: '' };
  active.value.body ||= {};
  const normalizedBody = Array.isArray(active.value.body) && active.value.body.length === 0 ? {} : active.value.body;
  bodyJson.value = JSON.stringify(normalizedBody, null, 2);
  notice.value = '';
  error.value = '';
}

async function savePage() {
  if (!active.value) return;

  let parsedBody = {};

  try {
    parsedBody = JSON.parse(bodyJson.value || '{}');
  } catch {
    error.value = 'Page body JSON is invalid.';
    return;
  }

  saving.value = true;
  notice.value = '';
  error.value = '';

  try {
    await adminFetch(`/admin/pages/${active.value.id}`, {
      method: 'PUT',
      body: { title: active.value.title, body: parsedBody },
    });
    notice.value = 'Page saved.';
    await loadPages();
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Unable to save page.';
  } finally {
    saving.value = false;
  }
}

onMounted(loadPages);
</script>

<template>
  <AdminShell title="Content">
    <div class="two-col">
      <section class="content-panel">
        <h2>Pages</h2>
        <table class="admin-table">
          <thead><tr><th>Slug</th><th>Title</th><th></th></tr></thead>
          <tbody>
            <tr v-for="page in pages" :key="page.id">
              <td>{{ page.slug }}</td>
              <td>{{ page.title?.en }}</td>
              <td><button class="btn ghost small" type="button" @click="editPage(page)">Edit</button></td>
            </tr>
          </tbody>
        </table>
      </section>
      <form v-if="active" class="form-panel admin-form" @submit.prevent="savePage">
        <h2>Edit {{ active.slug }}</h2>
        <label><span>Title EN</span><input v-model="active.title.en"></label>
        <label><span>Title HY</span><input v-model="active.title.hy"></label>
        <label><span>Title RU</span><input v-model="active.title.ru"></label>
        <label>
          <span>Body JSON</span>
          <textarea v-model="bodyJson" rows="18" spellcheck="false"></textarea>
        </label>
        <button class="btn primary full" :disabled="saving">{{ saving ? 'Saving...' : 'Save page' }}</button>
        <p v-if="error" class="form-error">{{ error }}</p>
        <p v-if="notice" class="success-note">{{ notice }}</p>
      </form>
    </div>
  </AdminShell>
</template>
