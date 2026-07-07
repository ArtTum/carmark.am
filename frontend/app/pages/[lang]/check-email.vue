<script setup lang="ts">
const route = useRoute();
const lang = useLang();
const { pending, forgotPassword } = useAuth();

const email = computed(() => String(route.query.email || ''));
const resetUrl = computed(() => String(route.query.reset || ''));
const notice = ref('');
const error = ref('');

async function openEmailLink() {
  error.value = '';

  if (!resetUrl.value) {
    window.location.href = email.value ? `mailto:${email.value}` : 'mailto:';
    return;
  }

  try {
    const url = new URL(resetUrl.value, window.location.origin);

    if (url.origin === window.location.origin) {
      await navigateTo(`${url.pathname}${url.search}`);
      return;
    }

    window.location.href = resetUrl.value;
  } catch {
    error.value = 'Վերականգնման հղումը բացել չհաջողվեց։ Փորձեք կրկին ուղարկել։';
  }
}

async function resendEmail() {
  error.value = '';
  notice.value = '';

  if (!email.value) {
    error.value = 'Էլ. հասցեն բացակայում է։ Վերադարձեք և կրկին փորձեք։';
    return;
  }

  try {
    const response = await forgotPassword(email.value);
    notice.value = 'Նոր հղումն ուղարկվեց։';
    await navigateTo({
      path: `/${lang.value}/check-email`,
      query: {
        email: response.email || email.value,
        ...(response.reset_url ? { reset: response.reset_url } : {}),
      },
    }, { replace: true });
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Նոր հղումը ուղարկել չհաջողվեց։';
  }
}
</script>

<template>
  <main class="auth-flow-screen">
    <section class="auth-flow-card">
      <div class="auth-flow-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M4 7L10.94 11.34C11.59 11.75 12.41 11.75 13.06 11.34L20 7M6 18H18C19.1046 18 20 17.1046 20 16V8C20 6.89543 19.1046 6 18 6H6C4.89543 6 4 6.89543 4 8V16C4 17.1046 4.89543 18 6 18Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </div>

      <h1>Ստուգեք Ձեր Էլ․ հասցեն</h1>
      <p>
        Մենք գաղտնաբառի վերականգնման հղում ենք ուղարկել
        <strong>{{ email || 'ձեր էլ. հասցեին' }}</strong>
      </p>

      <button class="auth-flow-submit auth-flow-primary-action" type="button" @click="openEmailLink">
        Բացել էլ․ փոստի հավելվածը
      </button>

      <p v-if="notice" class="auth-flow-notice">{{ notice }}</p>
      <p v-if="error" class="form-error auth-flow-message">{{ error }}</p>

      <p class="auth-flow-resend">
        Չե՞ք ստացել նամակ,
        <button type="button" :disabled="pending" @click="resendEmail">
          {{ pending ? 'Ուղարկվում է...' : 'կրկին ուղարկել' }}
        </button>
      </p>

      <NuxtLink class="auth-flow-back" :to="`/${lang}/login`">
        <span aria-hidden="true">←</span>
        Վերադառնալ մուտք գործել
      </NuxtLink>
    </section>
  </main>
</template>
