<script setup lang="ts">
const route = useRoute();
const lang = useLang();
const { loggedIn, pending, initAuth, login } = useAuth();
const form = reactive({ email: '', password: '' });
const error = ref('');

const redirectTo = computed(() => {
  const redirect = String(route.query.redirect || `/${lang.value}/profile`);
  return redirect.startsWith(`/${lang.value}/`) ? redirect : `/${lang.value}/profile`;
});

async function submitLogin() {
  error.value = '';

  try {
    await login(form.email, form.password);
    await navigateTo(redirectTo.value);
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Մուտքը չհաջողվեց։ Ստուգեք էլ. հասցեն և գաղտնաբառը։';
  }
}

onMounted(async () => {
  await initAuth();
  if (loggedIn.value) await navigateTo(redirectTo.value);
});
</script>

<template>
  <main class="site-auth-page">
    <section class="site-auth-shell">
      <div class="site-auth-copy">
        <Logo :to="`/${lang}`" />
        <h1>Մուտք հաշիվ</h1>
        <p>Մուտք գործեք՝ պահպանված մեքենաները, որոնումները և վճարման տվյալները կառավարելու համար։</p>
      </div>

      <form class="site-auth-card" @submit.prevent="submitLogin">
        <label>
          <span>Էլ. հասցե</span>
          <input v-model="form.email" type="email" autocomplete="email" required>
        </label>
        <label>
          <span>Գաղտնաբառ</span>
          <input v-model="form.password" type="password" autocomplete="current-password" required>
        </label>

        <NuxtLink class="auth-forgot-link" :to="{ path: `/${lang}/forgot-password`, query: form.email ? { email: form.email } : {} }">
          Մոռացե՞լ եք գաղտնաբառը
        </NuxtLink>

        <p v-if="error" class="form-error">{{ error }}</p>
        <button class="btn primary full" type="submit" :disabled="pending">
          {{ pending ? 'Խնդրում ենք սպասել...' : 'Մուտք գործել' }}
        </button>
        <p class="auth-switch-note">
          Հաշիվ չունե՞ք
          <NuxtLink :to="{ path: `/${lang}/register`, query: route.query }">Գրանցվել</NuxtLink>
        </p>
      </form>
    </section>
  </main>
</template>
