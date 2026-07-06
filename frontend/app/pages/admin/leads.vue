<script setup lang="ts">
const leads = ref<any[]>([]);

async function loadLeads() {
  try {
    const response = await adminFetch('/admin/leads');
    leads.value = response.data || [];
  } catch {
    await navigateTo('/admin/login');
  }
}

async function updateStatus(lead: any) {
  await adminFetch(`/admin/leads/${lead.id}`, {
    method: 'PATCH',
    body: { status: lead.status },
  });
}

onMounted(loadLeads);
</script>

<template>
  <AdminShell title="Leads">
    <section class="content-panel">
      <table class="admin-table">
        <thead><tr><th>Customer</th><th>Vehicle</th><th>Message</th><th>Status</th></tr></thead>
        <tbody>
          <tr v-for="lead in leads" :key="lead.id">
            <td><strong>{{ lead.name }}</strong><br>{{ lead.email }}<br>{{ lead.phone }}</td>
            <td>{{ lead.vehicle?.make }} {{ lead.vehicle?.model }}<br><span class="muted">{{ lead.type }}</span></td>
            <td>{{ lead.message }}</td>
            <td>
              <select v-model="lead.status" @change="updateStatus(lead)">
                <option>new</option>
                <option>contacted</option>
                <option>won</option>
                <option>lost</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </AdminShell>
</template>
