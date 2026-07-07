<script setup lang="ts">
const props = defineProps<{ vehicleId?: number; type?: string }>();
const t = useT();
const sent = ref(false);
const pending = ref(false);
const error = ref('');
const form = reactive({ name: '', email: '', phone: '', message: '' });

async function submit() {
  if (pending.value) return;

  pending.value = true;
  error.value = '';
  sent.value = false;

  try {
    await $fetch(apiUrl('/leads'), {
      method: 'POST',
      body: { ...form, vehicle_id: props.vehicleId || null, type: props.type || 'contact' },
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
  <form class="lead-form" @submit.prevent="submit">
    <label><span>{{ t('form.name') }}</span><input v-model="form.name" placeholder="Ձեր անունը" required></label>
    <label><span>{{ t('form.email') }}</span><input v-model="form.email" type="email" placeholder="name@mail.com" required></label>
    <label><span>{{ t('form.phone') }}</span><input v-model="form.phone" placeholder="+374 55 222222"></label>
    <label><span>{{ t('form.message') }}</span><textarea v-model="form.message" rows="4" placeholder="Գրեք ձեր հաղորդագրությունը ..." /></label>
    <button class="btn primary full" :disabled="pending">{{ pending ? 'Ուղարկվում է...' : t('form.send') }}</button>
    <p v-if="error" class="form-error">{{ error }}</p>
    <p v-if="sent" class="success-note">{{ t('form.success') }}</p>
  </form>
</template>
