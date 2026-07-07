<script setup lang="ts">
const route = useRoute();
const lang = useLang();
const { loggedIn, pending, initAuth, register } = useAuth();
const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});
const error = ref('');

const redirectTo = computed(() => {
  const redirect = String(route.query.redirect || `/${lang.value}/profile?tab=personal`);
  return redirect.startsWith(`/${lang.value}/`) ? redirect : `/${lang.value}/profile?tab=personal`;
});

async function submitRegister() {
  error.value = '';

  if (form.password !== form.password_confirmation) {
    error.value = 'Գաղտնաբառերը չեն համընկնում։';
    return;
  }

  try {
    await register(form);
    await navigateTo(redirectTo.value);
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Գրանցումը չհաջողվեց։ Ստուգեք տվյալները։';
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
        <h1>Գրանցվել</h1>
        <p>Ստեղծեք հաշիվ՝ մեքենաներ պահելու, որոնումները պահպանելու և հայտերը կառավարելու համար։</p>
      </div>

      <form class="site-auth-card" @submit.prevent="submitRegister">
        <label>
          <span>Անուն</span>
          <input v-model="form.name" autocomplete="name" required>
        </label>
        <label>
          <span>Էլ. հասցե</span>
          <input v-model="form.email" type="email" autocomplete="email" required>
        </label>
        <label>
          <span>Գաղտնաբառ</span>
          <input v-model="form.password" type="password" autocomplete="new-password" required minlength="6">
        </label>
        <label>
          <span>Կրկնել գաղտնաբառը</span>
          <input v-model="form.password_confirmation" type="password" autocomplete="new-password" required minlength="6">
        </label>

        <p v-if="error" class="form-error">{{ error }}</p>
        <button class="btn primary full" type="submit" :disabled="pending">
          {{ pending ? 'Խնդրում ենք սպասել...' : 'Ստեղծել հաշիվ' }}
        </button>
        <p class="auth-switch-note">
          Արդեն ունե՞ք հաշիվ
          <NuxtLink :to="{ path: `/${lang}/login`, query: route.query }">Մուտք գործել</NuxtLink>
        </p>
      </form>
    </section>
  </main>
</template>
