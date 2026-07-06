export default defineNuxtConfig({
  compatibilityDate: '2025-04-25',
  devtools: { enabled: false },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://127.0.0.1:8000/api',
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL || 'http://127.0.0.1:3000',
    },
  },
  app: {
    head: {
      charset: 'utf-8',
      viewport: 'width=device-width, initial-scale=1',
      title: 'CarMark',
      meta: [
        { name: 'format-detection', content: 'telephone=no' },
        { name: 'description', content: 'CarMark auto auction marketplace' },
      ],
    },
  },
  css: ['~/assets/css/main.css'],
});

