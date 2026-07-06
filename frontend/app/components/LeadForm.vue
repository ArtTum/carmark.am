<script setup lang="ts">
const props = defineProps<{ vehicleId?: number; type?: string }>();
const t = useT();
const sent = ref(false);
const form = reactive({ name: '', email: '', phone: '', message: '' });

async function submit() {
  await $fetch(apiUrl('/leads'), {
    method: 'POST',
    body: { ...form, vehicle_id: props.vehicleId || null, type: props.type || 'contact' },
  });
  sent.value = true;
  Object.assign(form, { name: '', email: '', phone: '', message: '' });
}
</script>

<template>
  <form class="lead-form" @submit.prevent="submit">
    <label><span>{{ t('form.name') }}</span><input v-model="form.name" required></label>
    <label><span>{{ t('form.email') }}</span><input v-model="form.email" type="email" required></label>
    <label><span>{{ t('form.phone') }}</span><input v-model="form.phone"></label>
    <label><span>{{ t('form.message') }}</span><textarea v-model="form.message" rows="4" /></label>
    <button class="btn primary full">{{ t('form.send') }}</button>
    <p v-if="sent" class="success-note">Request sent.</p>
  </form>
</template>

