<script setup lang="ts">
const form = reactive({ email: 'admin@carmark.am', password: 'admin123' });
const loading = ref(false);
const error = ref('');

async function login() {
  loading.value = true;
  error.value = '';
  try {
    const response = await $fetch<any>(apiUrl('/admin/login'), {
      method: 'POST',
      body: form,
    });
    localStorage.setItem('adminToken', response.token);
    await navigateTo('/admin');
  } catch (exception: any) {
    error.value = exception?.data?.message || 'Unable to sign in.';
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <main class="login-page">
    <form class="login-card admin-form" @submit.prevent="login">
      <Logo />
      <h1>Admin login</h1>
      <label><span>Email</span><input v-model="form.email" type="email" required></label>
      <label><span>Password</span><input v-model="form.password" type="password" required></label>
      <button class="btn primary full" :disabled="loading">{{ loading ? 'Signing in...' : 'Sign in' }}</button>
      <p v-if="error" class="error-note">{{ error }}</p>
    </form>
  </main>
</template>
