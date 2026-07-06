<script setup lang="ts">
defineProps<{ title: string }>();

function logout() {
  localStorage.removeItem('adminToken');
  navigateTo('/admin/login');
}
</script>

<template>
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <Logo to="/hy" />
      <nav>
        <NuxtLink to="/admin">Dashboard</NuxtLink>
        <NuxtLink to="/admin/vehicles">Vehicles</NuxtLink>
        <NuxtLink to="/admin/leads">Leads</NuxtLink>
        <NuxtLink to="/admin/content">Content</NuxtLink>
        <NuxtLink to="/admin/settings">Settings</NuxtLink>
      </nav>
      <button class="btn ghost full" @click="logout">Log out</button>
    </aside>
    <main class="admin-main">
      <header class="admin-top">
        <h1>{{ title }}</h1>
        <NuxtLink class="btn ghost small" to="/hy">View site</NuxtLink>
      </header>
      <slot />
    </main>
  </div>
</template>
