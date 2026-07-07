<script setup lang="ts">
const leads = ref<any[]>([]);
const updatingLeadId = ref<number | null>(null);
const notice = ref('');
const error = ref('');

async function loadLeads() {
  try {
    const response = await adminFetch('/admin/leads');
    leads.value = response.data || [];
  } catch {
    await navigateTo('/admin/login');
  }
}

async function updateStatus(lead: any) {
  updatingLeadId.value = lead.id;
  notice.value = '';
  error.value = '';

  try {
    await adminFetch(`/admin/leads/${lead.id}`, {
      method: 'PATCH',
      body: { status: lead.status },
    });
    notice.value = 'Lead status updated.';
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Unable to update lead.';
    await loadLeads();
  } finally {
    updatingLeadId.value = null;
  }
}

onMounted(loadLeads);
</script>

<template>
  <AdminShell title="Leads">
    <section class="content-panel">
      <p v-if="error" class="form-error">{{ error }}</p>
      <p v-if="notice" class="success-note">{{ notice }}</p>
      <table class="admin-table">
        <thead><tr><th>Customer</th><th>Vehicle</th><th>Message</th><th>Status</th></tr></thead>
        <tbody>
          <tr v-for="lead in leads" :key="lead.id">
            <td><strong>{{ lead.name }}</strong><br>{{ lead.email }}<br>{{ lead.phone }}</td>
            <td>{{ lead.vehicle?.make }} {{ lead.vehicle?.model }}<br><span class="muted">{{ lead.type }}</span></td>
            <td>{{ lead.message }}</td>
            <td>
              <select v-model="lead.status" :disabled="updatingLeadId === lead.id" @change="updateStatus(lead)">
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
