<script setup lang="ts">
const route = useRoute();
const lang = useLang();
const { pending, forgotPassword } = useAuth();

const email = ref(String(route.query.email || ''));
const error = ref('');

async function submitForgotPassword() {
  error.value = '';

  try {
    const response = await forgotPassword(email.value.trim());
    await navigateTo({
      path: `/${lang.value}/check-email`,
      query: {
        email: response.email || email.value.trim(),
        ...(response.reset_url ? { reset: response.reset_url } : {}),
      },
    });
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Չհաջողվեց ուղարկել հղումը։ Ստուգեք էլ. հասցեն։';
  }
}
</script>

<template>
  <main class="auth-flow-screen">
    <section class="auth-flow-card">
      <div class="auth-flow-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M10.5 13.5L8 16M14 10C15.1046 10 16 9.10457 16 8C16 6.89543 15.1046 6 14 6C12.8954 6 12 6.89543 12 8C12 8.38405 12.1082 8.74283 12.2958 9.0475L8.0475 13.2958C7.74283 13.1082 7.38405 13 7 13C5.89543 13 5 13.8954 5 15C5 16.1046 5.89543 17 7 17C8.10457 17 9 16.1046 9 15C9 14.6159 8.89184 14.2572 8.70416 13.9525L12.9525 9.70416C13.2572 9.89184 13.6159 10 14 10Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </div>

      <h1>Մոռացե՞լ եք գաղտնաբառը</h1>
      <p>Մի անհանգստացեք, մենք Ձեզ կուղարկենք վերականգնման հրահանգներ</p>

      <form class="auth-flow-form" @submit.prevent="submitForgotPassword">
        <label>
          <span>Էլ. հասցե</span>
          <input v-model="email" type="email" autocomplete="email" placeholder="email.com" required>
        </label>

        <p v-if="error" class="form-error auth-flow-message">{{ error }}</p>
        <button class="auth-flow-submit" type="submit" :disabled="pending">
          {{ pending ? 'Ուղարկվում է...' : 'Ուղարկել' }}
        </button>
      </form>

      <NuxtLink class="auth-flow-back" :to="`/${lang}/login`">
        <span aria-hidden="true">←</span>
        Վերադառնալ մուտք գործել
      </NuxtLink>
    </section>
  </main>
</template>
