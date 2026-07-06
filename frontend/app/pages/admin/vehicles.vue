<script setup lang="ts">
const vehicles = ref<any[]>([]);
const saving = ref(false);
const notice = ref('');

const emptyVehicle = () => ({
  id: null,
  slug: '',
  title: { hy: '', en: '', ru: '' },
  lot: '',
  vin: '',
  year: new Date().getFullYear(),
  make: '',
  model: '',
  body: 'Sedan',
  engine: '',
  transmission: 'Automatic',
  fuel: 'Gasoline',
  color: '',
  condition: 'Run and Drive',
  location: 'USA',
  auction: 'Copart',
  sale_date: '',
  odometer: '',
  current_bid: 0,
  buy_now: 0,
  shipping_fee: 0,
  featured: false,
  private_sale: false,
  status: 'published',
  imagesText: '/assets/extracted/03-17064740bf1e.jpg',
});

const form = reactive<any>(emptyVehicle());

async function loadVehicles() {
  try {
    const response = await adminFetch('/admin/vehicles');
    vehicles.value = response.data || [];
  } catch {
    await navigateTo('/admin/login');
  }
}

function resetForm() {
  Object.assign(form, emptyVehicle());
}

function editVehicle(vehicle: any) {
  Object.assign(form, {
    ...emptyVehicle(),
    ...vehicle,
    title: { hy: vehicle.title?.hy || '', en: vehicle.title?.en || '', ru: vehicle.title?.ru || '' },
    sale_date: vehicle.sale_date ? vehicle.sale_date.slice(0, 16) : '',
    imagesText: (vehicle.images || []).join('\n'),
  });
}

function vehiclePayload() {
  const payload = {
    ...form,
    year: Number(form.year),
    current_bid: Number(form.current_bid),
    buy_now: Number(form.buy_now || 0),
    shipping_fee: Number(form.shipping_fee || 0),
    sale_date: form.sale_date || null,
    images: String(form.imagesText || '').split(/\r?\n/).map((item) => item.trim()).filter(Boolean),
  };
  delete payload.id;
  delete payload.imagesText;
  return payload;
}

async function saveVehicle() {
  saving.value = true;
  notice.value = '';
  const endpoint = form.id ? `/admin/vehicles/${form.id}` : '/admin/vehicles';
  const method = form.id ? 'PUT' : 'POST';
  try {
    await adminFetch(endpoint, { method, body: vehiclePayload() });
    notice.value = form.id ? 'Vehicle updated.' : 'Vehicle created.';
    resetForm();
    await loadVehicles();
  } finally {
    saving.value = false;
  }
}

async function deleteVehicle(vehicle: any) {
  if (!confirm(`Delete ${vehicle.make} ${vehicle.model}?`)) return;
  await adminFetch(`/admin/vehicles/${vehicle.id}`, { method: 'DELETE' });
  await loadVehicles();
}

onMounted(loadVehicles);
</script>

<template>
  <AdminShell title="Vehicles">
    <div class="two-col">
      <section class="content-panel">
        <div class="section-head">
          <div>
            <p class="eyebrow">{{ vehicles.length }} records</p>
            <h2>Inventory</h2>
          </div>
          <button class="btn ghost small" @click="resetForm">New</button>
        </div>
        <table class="admin-table">
          <thead><tr><th>Car</th><th>Auction</th><th>Price</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <tr v-for="vehicle in vehicles" :key="vehicle.id">
              <td><strong>{{ vehicle.title?.en }}</strong><br><span class="muted">{{ vehicle.lot }}</span></td>
              <td>{{ vehicle.auction }}<br>{{ vehicle.sale_date?.slice(0, 10) }}</td>
              <td>{{ money(vehicle.current_bid) }}</td>
              <td><span class="badge">{{ vehicle.status }}</span></td>
              <td class="admin-actions">
                <button class="btn ghost small" @click="editVehicle(vehicle)">Edit</button>
                <button class="btn ghost small" @click="deleteVehicle(vehicle)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <form class="form-panel admin-form" @submit.prevent="saveVehicle">
        <h2>{{ form.id ? 'Edit vehicle' : 'New vehicle' }}</h2>
        <label><span>Title EN</span><input v-model="form.title.en" required></label>
        <label><span>Title HY</span><input v-model="form.title.hy"></label>
        <label><span>Title RU</span><input v-model="form.title.ru"></label>
        <div class="grid" style="grid-template-columns: 1fr 1fr;">
          <label><span>Year</span><input v-model.number="form.year" type="number" required></label>
          <label><span>Status</span><select v-model="form.status"><option>published</option><option>draft</option><option>archived</option></select></label>
          <label><span>Make</span><input v-model="form.make" required></label>
          <label><span>Model</span><input v-model="form.model" required></label>
          <label><span>Lot</span><input v-model="form.lot"></label>
          <label><span>VIN</span><input v-model="form.vin"></label>
          <label><span>Body</span><input v-model="form.body"></label>
          <label><span>Fuel</span><input v-model="form.fuel"></label>
          <label><span>Engine</span><input v-model="form.engine"></label>
          <label><span>Transmission</span><input v-model="form.transmission"></label>
          <label><span>Auction</span><input v-model="form.auction"></label>
          <label><span>Sale date</span><input v-model="form.sale_date" type="datetime-local"></label>
          <label><span>Current bid</span><input v-model.number="form.current_bid" type="number" required></label>
          <label><span>Buy now</span><input v-model.number="form.buy_now" type="number"></label>
        </div>
        <label><span>Location</span><input v-model="form.location"></label>
        <label><span>Condition</span><input v-model="form.condition"></label>
        <label><span>Odometer</span><input v-model="form.odometer"></label>
        <label><span>Images, one URL per line</span><textarea v-model="form.imagesText" rows="4"></textarea></label>
        <label><span><input v-model="form.featured" type="checkbox"> Featured</span></label>
        <label><span><input v-model="form.private_sale" type="checkbox"> Private sale</span></label>
        <button class="btn primary full" :disabled="saving">{{ saving ? 'Saving...' : 'Save vehicle' }}</button>
        <p v-if="notice" class="success-note">{{ notice }}</p>
      </form>
    </div>
  </AdminShell>
</template>
