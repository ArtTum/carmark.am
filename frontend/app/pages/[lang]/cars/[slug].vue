<script setup lang="ts">
const t = useT();
const lang = useLang();
const route = useRoute();
const bidSent = ref(false);
const bid = reactive({ amount: '', name: '', email: '' });

const { data } = await useFetch<any>(apiUrl(`/vehicles/${route.params.slug}`));
const vehicle = computed(() => data.value?.data);
const related = computed(() => data.value?.related || []);
const mainImage = computed(() => vehicle.value?.images?.[0] || '/assets/extracted/03-17064740bf1e.jpg');

async function submitBid() {
  await $fetch(apiUrl('/bids'), {
    method: 'POST',
    body: {
      vehicle_id: vehicle.value.id,
      amount: Number(bid.amount),
      name: bid.name,
      email: bid.email,
    },
  });
  bidSent.value = true;
  Object.assign(bid, { amount: '', name: '', email: '' });
}
</script>

<template>
  <main v-if="vehicle">
    <section class="page-hero">
      <div class="container vehicle-title">
        <p class="eyebrow">{{ vehicle.lot }} · {{ vehicle.auction }}</p>
        <h1>{{ localize(vehicle.title) }}</h1>
        <p>{{ vehicle.year }} {{ vehicle.make }} {{ vehicle.model }} · {{ vehicle.location }}</p>
      </div>
    </section>

    <section class="section">
      <div class="container two-col">
        <div class="vehicle-gallery">
          <img class="vehicle-main-image" :src="mainImage" :alt="localize(vehicle.title)">
          <div class="thumbs">
            <img v-for="image in vehicle.images" :key="image" :src="image" :alt="localize(vehicle.title)">
          </div>
          <div class="form-panel">
            <h2>Vehicle details</h2>
            <div class="vehicle-specs">
              <div class="spec-row"><span>VIN</span><strong>{{ vehicle.vin }}</strong></div>
              <div class="spec-row"><span>Condition</span><strong>{{ vehicle.condition }}</strong></div>
              <div class="spec-row"><span>Engine</span><strong>{{ vehicle.engine }}</strong></div>
              <div class="spec-row"><span>Transmission</span><strong>{{ vehicle.transmission }}</strong></div>
              <div class="spec-row"><span>Odometer</span><strong>{{ vehicle.odometer }}</strong></div>
              <div class="spec-row"><span>Sale date</span><strong>{{ vehicle.sale_date?.slice(0, 16).replace('T', ' ') }}</strong></div>
            </div>
          </div>
        </div>

        <aside class="form-panel">
          <p class="eyebrow">Current bid</p>
          <p class="price">{{ money(vehicle.current_bid) }}</p>
          <div class="vehicle-specs">
            <div class="spec-row"><span>Buy now</span><strong>{{ money(vehicle.buy_now) }}</strong></div>
            <div class="spec-row"><span>Shipping</span><strong>{{ money(vehicle.shipping_fee) }}</strong></div>
          </div>
          <form class="lead-form" @submit.prevent="submitBid">
            <label><span>Bid amount</span><input v-model="bid.amount" type="number" min="100" required></label>
            <label><span>{{ t('form.name') }}</span><input v-model="bid.name"></label>
            <label><span>{{ t('form.email') }}</span><input v-model="bid.email" type="email" required></label>
            <button class="btn primary full">{{ t('btn.bid') }}</button>
            <p v-if="bidSent" class="success-note">Bid submitted.</p>
          </form>
          <hr>
          <h3>Ask about this car</h3>
          <LeadForm :vehicle-id="vehicle.id" type="vehicle" />
        </aside>
      </div>
    </section>

    <section class="section soft">
      <div class="container">
        <div class="section-head">
          <div>
            <p class="eyebrow">More options</p>
            <h2>Related vehicles</h2>
          </div>
          <NuxtLink class="btn ghost" :to="`/${lang}/inventory`">{{ t('inventory.title') }}</NuxtLink>
        </div>
        <div class="grid cars-grid">
          <VehicleCard v-for="item in related" :key="item.id" :vehicle="item" />
        </div>
      </div>
    </section>
  </main>
</template>
