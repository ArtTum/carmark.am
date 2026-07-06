<script setup lang="ts">
const pages = ref<any[]>([]);
const active = ref<any>(null);
const notice = ref('');

async function loadPages() {
  try {
    const response = await adminFetch('/admin/pages');
    pages.value = response.data || [];
    active.value ||= pages.value[0] ? JSON.parse(JSON.stringify(pages.value[0])) : null;
  } catch {
    await navigateTo('/admin/login');
  }
}

function editPage(page: any) {
  active.value = JSON.parse(JSON.stringify(page));
  notice.value = '';
}

async function savePage() {
  await adminFetch(`/admin/pages/${active.value.id}`, {
    method: 'PUT',
    body: { title: active.value.title, body: active.value.body },
  });
  notice.value = 'Page saved.';
  await loadPages();
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
              <td><button class="btn ghost small" @click="editPage(page)">Edit</button></td>
            </tr>
          </tbody>
        </table>
      </section>
      <form v-if="active" class="form-panel admin-form" @submit.prevent="savePage">
        <h2>Edit {{ active.slug }}</h2>
        <label><span>Title EN</span><input v-model="active.title.en"></label>
        <label><span>Title HY</span><input v-model="active.title.hy"></label>
        <label><span>Title RU</span><input v-model="active.title.ru"></label>
        <label><span>Body EN</span><textarea v-model="active.body.en" rows="5"></textarea></label>
        <label><span>Body HY</span><textarea v-model="active.body.hy" rows="5"></textarea></label>
        <label><span>Body RU</span><textarea v-model="active.body.ru" rows="5"></textarea></label>
        <button class="btn primary full">Save page</button>
        <p v-if="notice" class="success-note">{{ notice }}</p>
      </form>
    </div>
  </AdminShell>
</template>
