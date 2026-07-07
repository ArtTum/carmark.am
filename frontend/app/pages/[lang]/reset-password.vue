<script setup lang="ts">
const route = useRoute();
const lang = useLang();
const { pending, resetPassword } = useAuth();

const form = reactive({
  password: '',
  password_confirmation: '',
});
const error = ref('');

const email = computed(() => String(route.query.email || ''));
const token = computed(() => String(route.query.token || ''));

async function submitResetPassword() {
  error.value = '';

  if (!email.value || !token.value) {
    error.value = 'Վերականգնման հղումը սխալ է կամ ժամկետանց։';
    return;
  }

  if (form.password !== form.password_confirmation) {
    error.value = 'Գաղտնաբառերը չեն համընկնում։';
    return;
  }

  try {
    await resetPassword({
      email: email.value,
      token: token.value,
      password: form.password,
      password_confirmation: form.password_confirmation,
    });
    await navigateTo({
      path: `/${lang.value}/password-reset-success`,
      query: { email: email.value },
    });
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Գաղտնաբառը թարմացնել չհաջողվեց։';
  }
}
</script>

<template>
  <main class="auth-flow-screen">
    <section class="auth-flow-card">
      <div class="auth-flow-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M7 11V8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8V11M6.8 21H17.2C18.8802 21 19.7202 21 20.362 20.673C20.9265 20.3854 21.3854 19.9265 21.673 19.362C22 18.7202 22 17.8802 22 16.2V15.8C22 14.1198 22 13.2798 21.673 12.638C21.3854 12.0735 20.9265 11.6146 20.362 11.327C19.7202 11 18.8802 11 17.2 11H6.8C5.11984 11 4.27976 11 3.63803 11.327C3.07354 11.6146 2.6146 12.0735 2.32698 12.638C2 13.2798 2 14.1198 2 15.8V16.2C2 17.8802 2 18.7202 2.32698 19.362C2.6146 19.9265 3.07354 20.3854 3.63803 20.673C4.27976 21 5.11984 21 6.8 21Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </div>

      <h1>Ստեղծել նոր գաղտնաբառը</h1>
      <p>Ձեր նոր գաղտնաբառը պետք է տարբերվի նախկինում օգտագործված գաղտնաբառերից</p>

      <form class="auth-flow-form" @submit.prevent="submitResetPassword">
        <label>
          <span>Գաղտնաբառ</span>
          <input v-model="form.password" type="password" autocomplete="new-password" required minlength="6">
        </label>

        <label>
          <span>Հաստատել գաղտնաբառը</span>
          <input v-model="form.password_confirmation" type="password" autocomplete="new-password" required minlength="6">
        </label>

        <p v-if="error" class="form-error auth-flow-message">{{ error }}</p>
        <button class="auth-flow-submit" type="submit" :disabled="pending">
          {{ pending ? 'Պահպանվում է...' : 'Հաստատել' }}
        </button>
      </form>

      <NuxtLink class="auth-flow-back" :to="`/${lang}/login`">
        <span aria-hidden="true">←</span>
        Վերադառնալ մուտք գործել
      </NuxtLink>
    </section>
  </main>
</template>
