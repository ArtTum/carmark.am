<script setup lang="ts">
const t = useT();
const sent = ref(false);
const pending = ref(false);
const error = ref('');
const settings = useSiteSettings();
const form = reactive({ name: '', email: '', phone: '', message: '' });

const offices = [
  ['Երևան, Խանջյան 41', 'info@carmark.am', '+374 96 830001'],
  ['Երևան, Մարգարյան 1', 'info@carmark.am', '+374 43 830001'],
  ['Երևան, Կիլիկիա 2', 'info@platinummotors.am', '+374 44 578009'],
  ['Երևան, Ներքինյան 1', 'info@platinummotors.am', '+374 33 578009'],
];

const partners = [
  ['Վրաստան', 'info@iaa.ge', 'www.iaa.ge', '+955 577 32 09 09'],
  ['Ուկրաինա', 'kiev@iaamotors.com', 'www.iaamotors.com', '+38 098 098 80 00'],
];

const pageContent = usePageContent('contact', {
  offices,
  partners,
  map_image: '/assets/design/contact-map.png',
  office_image: '/assets/design/contact-office.png',
});
const renderedOffices = computed(() => pageContent.value.offices?.length ? pageContent.value.offices : offices);
const renderedPartners = computed(() => pageContent.value.partners?.length ? pageContent.value.partners : partners);

async function submit() {
  if (pending.value) return;

  pending.value = true;
  error.value = '';
  sent.value = false;

  try {
    await $fetch(apiUrl('/leads'), {
      method: 'POST',
      body: { ...form, type: 'contact' },
    });
    sent.value = true;
    Object.assign(form, { name: '', email: '', phone: '', message: '' });
  } catch {
    error.value = 'Հայտը հնարավոր չեղավ ուղարկել։ Փորձեք մի փոքր ուշ։';
  } finally {
    pending.value = false;
  }
}
</script>

<template>
  <main class="contact-page">
    <section class="contact-hero">
      <div class="contact-form-pane">
        <h1>{{ t('nav.contact') }}</h1>
        <p>Կարող եք կապվել մեզ հետ ցանկացած ժամանակ</p>
        <form class="contact-form" @submit.prevent="submit">
          <label><span>Անուն Ազգանուն</span><input v-model="form.name" placeholder="Ձեր անունը" required></label>
          <label><span>{{ t('form.email') }}</span><input v-model="form.email" type="email" placeholder="Ձեր էլ. հասցեն" required></label>
          <label><span>{{ t('form.phone') }}</span><input v-model="form.phone" placeholder="+374 55 222222"></label>
          <label><span>{{ t('form.message') }}</span><textarea v-model="form.message" placeholder="Գրեք ձեր հաղորդագրությունը ..." rows="6" /></label>
          <button class="btn primary full" type="submit" :disabled="pending">{{ pending ? 'Ուղարկվում է...' : t('form.send') }}</button>
          <p v-if="error" class="form-error">{{ error }}</p>
          <p v-if="sent" class="success-note">{{ t('form.success') }}</p>
        </form>
      </div>
      <div class="contact-map-pane">
        <img :src="pageContent.map_image" alt="CarMark map">
      </div>
    </section>

    <section class="section soft contact-locations">
      <div class="container location-grid">
        <h2>Մեր հասցեները</h2>
        <article v-for="office in renderedOffices" :key="localize(office[0]) || office[0]">
          <h3>{{ localize(office[0]) || office[0] }}</h3>
          <p>{{ localize(office[1]) || office[1] }}</p>
          <p>{{ localize(office[2]) || office[2] }}</p>
        </article>
        <h2>Մեր օտարերկրյա մասնաճյուղերը</h2>
        <article v-for="partner in renderedPartners" :key="localize(partner[0]) || partner[0]">
          <h3>{{ localize(partner[0]) || partner[0] }}</h3>
          <p>{{ localize(partner[1]) || partner[1] }}</p>
          <p>{{ localize(partner[2]) || partner[2] }}</p>
          <p>{{ localize(partner[3]) || partner[3] }}</p>
        </article>
      </div>
    </section>

    <section class="section contact-cta">
      <div class="container">
        <div class="center-head">
          <h2>Կապվեք մեզ հետ</h2>
          <p class="muted">Զրուցեք մեր պրոֆեսիոնալ թիմի հետ</p>
        </div>
        <img class="office-photo" :src="pageContent.office_image" alt="CarMark office">
        <div class="contact-card-grid">
          <article class="contact-card">
            <span class="red-square-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5v-11Z" /><path d="M7 9l5 3.5L17 9" /></svg>
            </span>
            <h3>Գրեք մեզ</h3>
            <p>Կարող եք կապվել մեր թիմի հետ</p>
            <strong>{{ settings.email || 'info@carmark.am' }}</strong>
          </article>
          <article class="contact-card">
            <span class="red-square-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24"><path d="M6.5 5.5 9 5l1.5 4-1.8 1.1a10 10 0 0 0 5.2 5.2l1.1-1.8 4 1.5-.5 2.5c-.2 1-1 1.5-2 1.5C10 19 5 14 5 7.5c0-1 .5-1.8 1.5-2Z" /></svg>
            </span>
            <h3>Զանգահարեք մեզ</h3>
            <p>Երկ, Երք, Չոր, Հնգ, Ուրբ 09:00-18:00</p>
            <strong>{{ settings.phone || '+374 96 830001' }}</strong>
          </article>
        </div>
      </div>
    </section>
  </main>
</template>
