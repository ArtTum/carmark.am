<script setup lang="ts">
const form = reactive({
  bid: 6500,
  auctionFee: 850,
  shipping: 1700,
  service: 400,
  customs: 1200,
});

const total = computed(() => Number(form.bid) + Number(form.auctionFee) + Number(form.shipping) + Number(form.service) + Number(form.customs));
</script>

<template>
  <main>
    <section class="page-hero">
      <div class="container">
        <p class="eyebrow">Cost planning</p>
        <h1>Calculator</h1>
        <p>Estimate the real landed price before sending a bid request.</p>
      </div>
    </section>
    <section class="section">
      <div class="container calculator-grid">
        <form class="form-panel admin-form">
          <label><span>Bid price</span><input v-model.number="form.bid" type="number"></label>
          <label><span>Auction fee</span><input v-model.number="form.auctionFee" type="number"></label>
          <label><span>Shipping</span><input v-model.number="form.shipping" type="number"></label>
          <label><span>Service fee</span><input v-model.number="form.service" type="number"></label>
          <label><span>Customs</span><input v-model.number="form.customs" type="number"></label>
        </form>
        <aside class="content-panel summary-box">
          <div class="summary-row"><span>Bid</span><strong>{{ money(form.bid) }}</strong></div>
          <div class="summary-row"><span>Auction fee</span><strong>{{ money(form.auctionFee) }}</strong></div>
          <div class="summary-row"><span>Shipping</span><strong>{{ money(form.shipping) }}</strong></div>
          <div class="summary-row"><span>Service</span><strong>{{ money(form.service) }}</strong></div>
          <div class="summary-row"><span>Customs</span><strong>{{ money(form.customs) }}</strong></div>
          <div class="summary-row total"><span>Total</span><strong>{{ money(total) }}</strong></div>
        </aside>
      </div>
    </section>
  </main>
</template>
