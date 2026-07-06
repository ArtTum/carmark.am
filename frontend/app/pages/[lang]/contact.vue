<script setup lang="ts">
const { data } = await useFetch<any>(apiUrl('/site'));
const settings = computed(() => data.value?.settings || {});
</script>

<template>
  <main>
    <section class="page-hero">
      <div class="container">
        <p class="eyebrow">Contact</p>
        <h1>{{ localize({ hy: 'Կապ մեզ հետ', en: 'Contact us', ru: 'Связаться с нами' }) }}</h1>
        <p>{{ localize(settings.address || {}) }}</p>
      </div>
    </section>
    <section class="section">
      <div class="container two-col">
        <div class="content-panel">
          <h2>CarMark office</h2>
          <div class="vehicle-specs">
            <div class="spec-row"><span>Email</span><strong>{{ settings.email }}</strong></div>
            <div class="spec-row"><span>Phone</span><strong>{{ settings.phone }}</strong></div>
            <div class="spec-row"><span>Address</span><strong>{{ localize(settings.address || {}) }}</strong></div>
          </div>
        </div>
        <div class="form-panel">
          <h2>Send request</h2>
          <LeadForm type="contact" />
        </div>
      </div>
    </section>
  </main>
</template>
