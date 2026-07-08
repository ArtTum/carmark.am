<script setup lang="ts">
const props = defineProps<{ vehicle: any; wide?: boolean }>();
const t = useT();
const lang = useLang();
const watched = ref(false);
const currentSlide = ref(0);
const shareNote = ref('');

const images = computed(() => {
  const list = Array.isArray(props.vehicle.images) ? props.vehicle.images.filter(Boolean) : [];
  return list.length ? list : ['/assets/extracted/03-17064740bf1e.jpg'];
});

const imageCount = computed(() => images.value.length);
const currentImage = computed(() => images.value[currentSlide.value] || images.value[0]);
const cardTitle = computed(() => localize(props.vehicle.title) || `${props.vehicle.year || ''} ${props.vehicle.make || ''} ${props.vehicle.model || ''}`.trim());
const saleDate = computed(() => props.vehicle.sale_date?.slice(0, 16).replace('T', ' ') || '2026 Հլս 15 - 7:00 AM');
const detailRows = computed(() => [
  ['Lot #', props.vehicle.lot || '12555884564'],
  ['Օդոմետր', props.vehicle.odometer || '117,630 կմ'],
  ['Վնասվածք', props.vehicle.condition || 'Ֆրոնտ վնասվածք'],
  ['Վաճառք', saleDate.value],
  ['Աճուրդ', props.vehicle.auction || 'Copart'],
  ['Գույն', props.vehicle.color || 'Սպիտակ'],
  ['Փոխանցման տուփ', props.vehicle.transmission || 'Ավտոմատ'],
  ['Շարժիչ', props.vehicle.engine || '3.6 L'],
  ['Վառելիք', props.vehicle.fuel || 'Բենզին'],
]);

watch(() => props.vehicle.id, () => {
  currentSlide.value = 0;
});

function readWatchlist() {
  if (!import.meta.client) return [];

  try {
    const value = JSON.parse(localStorage.getItem('watchlist') || '[]');
    return Array.isArray(value) ? value.map(String) : [];
  } catch {
    localStorage.removeItem('watchlist');
    return [];
  }
}

onMounted(() => {
  const ids = readWatchlist();
  watched.value = ids.includes(String(props.vehicle.id));
});

function toggleWatch() {
  const id = String(props.vehicle.id);
  const ids = readWatchlist();
  const next = ids.includes(id) ? ids.filter((item: string) => item !== id) : [...ids, id];
  localStorage.setItem('watchlist', JSON.stringify(next));
  watched.value = next.includes(id);
}

function changeSlide(direction: number) {
  if (imageCount.value < 2) return;
  currentSlide.value = (currentSlide.value + direction + imageCount.value) % imageCount.value;
}

function setSlide(index: number) {
  currentSlide.value = index;
}

async function shareVehicle() {
  if (!import.meta.client) return;

  const url = `${window.location.origin}/${lang.value}/cars/${props.vehicle.slug}`;
  shareNote.value = '';

  try {
    if (navigator.share) {
      await navigator.share({ title: cardTitle.value, url });
      shareNote.value = 'Հղումը պատրաստ է';
    } else if (navigator.clipboard) {
      await navigator.clipboard.writeText(url);
      shareNote.value = 'Հղումը պատճենվեց';
    } else {
      shareNote.value = url;
    }
  } catch {
    shareNote.value = 'Չհաջողվեց կիսվել';
  }

  window.setTimeout(() => {
    shareNote.value = '';
  }, 2200);
}
</script>

<template>
  <article class="car-card" :class="{ wide }">
    <div class="car-media">
      <NuxtLink class="car-media-link" :to="`/${lang}/cars/${vehicle.slug}`">
        <img :src="currentImage" :alt="cardTitle">
      </NuxtLink>

      <span v-if="wide" class="image-count-badge">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M4 7a2 2 0 0 1 2-2h2l1.4-1.5h5.2L16 5h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z" />
          <path d="M12 16a3.2 3.2 0 1 0 0-6.4 3.2 3.2 0 0 0 0 6.4Z" />
        </svg>
        {{ imageCount }}
      </span>

      <button class="watch-btn" :class="{ active: watched }" type="button" aria-label="Save vehicle" @click.stop="toggleWatch">
        <svg width="23" height="20" viewBox="0 0 23 20" fill="none" aria-hidden="true">
          <path d="M6.31055 0.800781H6.3291C8.08439 0.791775 9.75218 1.51194 10.9121 2.7666L11.5 3.40137L12.0879 2.7666C13.2478 1.51194 14.9156 0.791775 16.6709 0.800781H16.6895C18.1503 0.774683 19.5554 1.31766 20.5898 2.29883C21.6234 3.27921 22.2004 4.61427 22.2002 6.00098C22.2001 8.64038 20.5684 11.053 18.251 13.3281C17.1044 14.4537 15.8236 15.5136 14.5557 16.5234C13.5011 17.3634 12.4401 18.1829 11.502 18.957C10.5642 18.1764 9.5048 17.3536 8.44922 16.5117C7.18087 15.5001 5.89946 14.4413 4.75195 13.3174C2.4331 11.0462 0.799909 8.64179 0.799805 6.00098C0.799557 4.61431 1.37661 3.27922 2.41016 2.29883C3.44464 1.31765 4.84971 0.774684 6.31055 0.800781Z" fill="currentColor" stroke="white" stroke-width="1.6" />
        </svg>
      </button>

      <template v-if="imageCount > 1">
        <button class="slider-btn slider-prev" type="button" aria-label="Previous image" @click.stop="changeSlide(-1)">
          <svg width="7" height="12" viewBox="0 0 7 12" fill="none" aria-hidden="true">
            <path d="M6 11L1 6L6 1" stroke="#101828" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
        <button class="slider-btn slider-next" type="button" aria-label="Next image" @click.stop="changeSlide(1)">
          <svg width="7" height="12" viewBox="0 0 7 12" fill="none" aria-hidden="true">
            <path d="M1 11L6 6L1 1" stroke="#101828" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
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
      <div v-if="wide" class="list-card-top">
        <h3><NuxtLink :to="`/${lang}/cars/${vehicle.slug}`">{{ cardTitle }}</NuxtLink></h3>
        <span class="list-card-links">
          <button type="button" @click="shareVehicle">Կիսվել</button>
          <button type="button" @click="toggleWatch">{{ watched ? 'Պահպանված' : 'Պահպանել' }}</button>
        </span>
      </div>
      <small v-if="wide && shareNote" class="list-share-note">{{ shareNote }}</small>
      <h3 v-else><NuxtLink :to="`/${lang}/cars/${vehicle.slug}`">{{ cardTitle }}</NuxtLink></h3>

      <p class="muted">{{ vehicle.lot }} · {{ vehicle.auction }}</p>
      <p>{{ saleDate }} · {{ vehicle.location || 'Los Angeles - CA' }}</p>

      <div class="car-meta">
        <span>{{ vehicle.year }}</span>
        <span>{{ vehicle.body }}</span>
        <span>{{ vehicle.fuel }}</span>
      </div>

      <div v-if="wide" class="list-spec-grid">
        <span v-for="row in detailRows" :key="row[0]">
          <small>{{ row[0] }}</small>
          <strong>{{ row[1] }}</strong>
        </span>
      </div>

      <div class="car-actions">
        <strong>{{ money(vehicle.current_bid) }}</strong>
        <span v-if="wide" class="list-action-group">
          <NuxtLink class="btn ghost small" :to="`/${lang}/cars/${vehicle.slug}`">Ընթացիկ ${{ vehicle.shipping_fee || 275 }}</NuxtLink>
          <NuxtLink class="btn outline-red small" :to="`/${lang}/cars/${vehicle.slug}`">{{ t('btn.bid') }}</NuxtLink>
          <NuxtLink class="btn primary small" :to="`/${lang}/cars/${vehicle.slug}`">{{ t('btn.buy') }} ({{ money(vehicle.buy_now || 25500) }})</NuxtLink>
        </span>
        <NuxtLink v-else class="btn ghost small" :to="`/${lang}/cars/${vehicle.slug}`">{{ t('btn.details') }}</NuxtLink>
      </div>
    </div>
  </article>
</template>
