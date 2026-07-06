<script setup lang="ts">
const stats = ref<any>(null);
const loading = ref(true);

onMounted(async () => {
  try {
    stats.value = await adminFetch('/admin/dashboard');
  } catch {
    await navigateTo('/admin/login');
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <AdminShell title="Dashboard">
    <p v-if="loading" class="muted">Loading dashboard...</p>
    <template v-else-if="stats">
      <div class="grid admin-grid">
        <article class="admin-card"><span>Total vehicles</span><strong>{{ stats.vehicles }}</strong></article>
        <article class="admin-card"><span>Published</span><strong>{{ stats.published }}</strong></article>
        <article class="admin-card"><span>Leads</span><strong>{{ stats.leads }}</strong></article>
        <article class="admin-card"><span>Bid value</span><strong>{{ money(stats.bid_value) }}</strong></article>
      </div>
      <section class="section">
        <div class="content-panel">
          <h2>Recent leads</h2>
          <table class="admin-table">
            <thead><tr><th>Name</th><th>Contact</th><th>Vehicle</th><th>Status</th></tr></thead>
            <tbody>
              <tr v-for="lead in stats.recent_leads" :key="lead.id">
                <td>{{ lead.name }}</td>
                <td>{{ lead.email }}<br>{{ lead.phone }}</td>
                <td>{{ lead.vehicle?.make }} {{ lead.vehicle?.model }}</td>
                <td><span class="badge">{{ lead.status }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>
  </AdminShell>
</template>
