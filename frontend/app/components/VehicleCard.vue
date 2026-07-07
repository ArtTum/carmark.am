<script setup lang="ts">
const props = defineProps<{ vehicle: any; wide?: boolean }>();
const t = useT();
const lang = useLang();
const watched = ref(false);
const currentSlide = ref(0);

const images = computed(() => {
  const list = Array.isArray(props.vehicle.images) ? props.vehicle.images.filter(Boolean) : [];
  return list.length ? list : ['/assets/extracted/03-17064740bf1e.jpg'];
});

const imageCount = computed(() => images.value.length);
const currentImage = computed(() => images.value[currentSlide.value] || images.value[0]);

watch(() => props.vehicle.id, () => {
  currentSlide.value = 0;
});

onMounted(() => {
  const ids = JSON.parse(localStorage.getItem('watchlist') || '[]');
  watched.value = ids.includes(props.vehicle.id);
});

function toggleWatch() {
  const ids = JSON.parse(localStorage.getItem('watchlist') || '[]');
  const next = ids.includes(props.vehicle.id) ? ids.filter((id: number) => id !== props.vehicle.id) : [...ids, props.vehicle.id];
  localStorage.setItem('watchlist', JSON.stringify(next));
  watched.value = next.includes(props.vehicle.id);
}

function changeSlide(direction: number) {
  if (imageCount.value < 2) return;
  currentSlide.value = (currentSlide.value + direction + imageCount.value) % imageCount.value;
}

function setSlide(index: number) {
  currentSlide.value = index;
}
</script>

<template>
  <article class="car-card" :class="{ wide }">
    <div class="car-media">
      <NuxtLink class="car-media-link" :to="`/${lang}/cars/${vehicle.slug}`">
        <img :src="currentImage" :alt="localize(vehicle.title)">
      </NuxtLink>
      <button class="watch-btn" :class="{ active: watched }" type="button" aria-label="Save vehicle" @click.stop="toggleWatch">
        <svg width="23" height="20" viewBox="0 0 23 20" fill="none" aria-hidden="true">
          <path d="M6.31055 0.800781H6.3291C8.08439 0.791775 9.75218 1.51194 10.9121 2.7666L11.5 3.40137L12.0879 2.7666C13.2478 1.51194 14.9156 0.791775 16.6709 0.800781H16.6895C18.1503 0.774683 19.5554 1.31766 20.5898 2.29883C21.6234 3.27921 22.2004 4.61427 22.2002 6.00098C22.2001 8.64038 20.5684 11.053 18.251 13.3281C17.1044 14.4537 15.8236 15.5136 14.5557 16.5234C13.5011 17.3634 12.4401 18.1829 11.502 18.957C10.5642 18.1764 9.5048 17.3536 8.44922 16.5117C7.18087 15.5001 5.89946 14.4413 4.75195 13.3174C2.4331 11.0462 0.799909 8.64179 0.799805 6.00098C0.799557 4.61431 1.37661 3.27922 2.41016 2.29883C3.44464 1.31765 4.84971 0.774684 6.31055 0.800781Z" fill="currentColor" stroke="white" stroke-width="1.6" />
        </svg>
      </button>
      <template v-if="imageCount > 1">
        <button class="slider-btn slider-prev" type="button" aria-label="Previous image" @click.stop="changeSlide(-1)">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <rect width="32" height="32" rx="16" fill="white" />
            <path d="M18.5 21L13.5 16L18.5 11" stroke="#101828" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <button class="slider-btn slider-next" type="button" aria-label="Next image" @click.stop="changeSlide(1)">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <rect width="32" height="32" rx="16" fill="white" />
            <path d="M13.5 21L18.5 16L13.5 11" stroke="#101828" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <div class="slider-dots">
          <button
            v-for="(_, index) in images"
            :key="index"
            class="slider-dot"
            :class="{ active: currentSlide === index }"
            type="button"
            :aria-label="`Show image ${index + 1}`"
            @click.stop="setSlide(index)"
          />
        </div>
      </template>
    </div>
    <div class="car-body">
      <h3><NuxtLink :to="`/${lang}/cars/${vehicle.slug}`">{{ localize(vehicle.title) }}</NuxtLink></h3>
      <p class="muted">{{ vehicle.lot }} · {{ vehicle.auction }}</p>
      <p>{{ vehicle.sale_date?.slice(0, 16).replace('T', ' ') }} · {{ vehicle.location }}</p>
      <div class="car-meta">
        <span>{{ vehicle.year }}</span>
        <span>{{ vehicle.body }}</span>
        <span>{{ vehicle.fuel }}</span>
      </div>
      <div class="car-actions">
        <strong>{{ money(vehicle.current_bid) }}</strong>
        <NuxtLink class="btn ghost small" :to="`/${lang}/cars/${vehicle.slug}`">{{ t('btn.details') }}</NuxtLink>
      </div>
    </div>
  </article>
</template>
