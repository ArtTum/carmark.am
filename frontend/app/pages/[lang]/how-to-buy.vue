<script setup lang="ts">
const lang = useLang();

const steps = [
  {
    title: 'Որոնել մեքենա',
    text: 'Մեր կայքի առաջարկվող կամ ֆիլտրով որոնվող մեքենաների ցանկից ընտրեք Ձեզ հարմար տարբերակը։',
    points: ['Ֆիլտրեք ըստ մակնիշի և տարեթվի', 'Համեմատեք մեքենայի գինը և վիճակը', 'Պահպանեք հետաքրքիր տարբերակները'],
    image: '/assets/design/products-page.png',
  },
  {
    title: 'Գրանցվեք մեր կայքում',
    text: 'Գրանցումը օգնում է պահպանել հայտերը, հետևել կարգավիճակին և արագ կապվել մասնագետի հետ։',
    points: ['Լրացրեք անձնական տվյալները', 'Հաստատեք էլ. հասցեն', 'Ստացեք անհատական աջակցություն'],
    image: '/assets/design/home-page.png',
  },
  {
    title: 'Բարձրացրեք աճուրդի գործընթացը',
    text: 'Հաստատեք բյուջեն և մասնակցեք աճուրդին մեր թիմի ուղեկցությամբ։',
    points: ['Նախապես հաշվարկեք վերջնական արժեքը', 'Հաստատեք աճուրդի առավելագույն գինը', 'Հետևեք աճուրդի ընթացքին'],
    image: '/assets/design/product-page.png',
  },
];

const bidOptions = [
  ['Առկա տեսականի', 'Արագ գնման համար ընտրեք CarMark-ի առկա ավտոմեքենաները։'],
  ['Առանց մասնակցելու աճուրդին', 'Կարող եք գնել մեքենա արդեն հաստատված արժեքով՝ առանց աճուրդի սպասելու։'],
  ['Ընտրել “Գնել հիմա”', 'Որոշ մեքենաների համար հասանելի է անմիջապես գնելու հնարավորություն։'],
];

const supportCards = [
  ['Որոնել և մասնակցել', 'Ամեն օր հասանելի են նոր աճուրդներ ու մեքենաներ։'],
  ['Ստացեք օգնություն', 'Մեր թիմը կօգնի ընտրել, ստուգել և հաշվարկել։'],
  ['Ստացեք ձեր ավտոն', 'Առաքումից մինչև հանձնում՝ ամբողջ ընթացքը վերահսկվում է։'],
];
const pageContent = usePageContent('how-to-buy', { steps, bidOptions, supportCards });
const renderedSteps = computed(() => pageContent.value.steps?.length ? pageContent.value.steps : steps);
const renderedBidOptions = computed(() => pageContent.value.bidOptions?.length ? pageContent.value.bidOptions : bidOptions);
const renderedSupportCards = computed(() => pageContent.value.supportCards?.length ? pageContent.value.supportCards : supportCards);
</script>

<template>
  <main class="static-page how-page">
    <section class="grid-hero">
      <div class="container">
        <h1>Քայլ առ քայլ հրահանգներ, թե ինչպես գնել մեքենա աճուրդից</h1>
        <p>Ինչպես գտնել, մասնակցել և ընդունել Ձեր մեքենան՝ ապահով ու կանխատեսելի գործընթացով։</p>
      </div>
    </section>

    <section class="section how-intro">
      <div class="container center-head">
        <p class="eyebrow">Ինչպես գնել մեքենա</p>
        <h2>Գնել մեքենա CarMark-ի միջոցով</h2>
        <p class="muted">Մեքենաների լայն տեսականի, արագ գրանցում և մասնագետների ուղեկցում յուրաքանչյուր փուլում։</p>
      </div>
    </section>

    <section class="how-steps">
      <article v-for="(step, index) in renderedSteps" :key="localize(step.title) || step.title" class="how-step" :class="{ flip: index % 2 === 1 }">
        <div class="container">
          <div class="how-step-copy">
            <span class="step-icon">{{ index + 1 }}</span>
            <h2>{{ localize(step.title) || step.title }}</h2>
            <p>{{ localize(step.text) || step.text }}</p>
            <ul>
              <li v-for="point in step.points || []" :key="localize(point) || point">{{ localize(point) || point }}</li>
            </ul>
          </div>
          <div class="mockup-frame">
            <img :src="step.image" :alt="localize(step.title) || step.title">
          </div>
        </div>
      </article>
    </section>

    <section class="section soft bid-guide">
      <div class="container bid-guide-grid">
        <div>
          <h2>Գնիր հիմա</h2>
          <p class="muted">Առանց աճուրդի սպասելու՝ ընտրեք մեքենան և ստացեք գնման քայլերի ամբողջական ուղեկցում։</p>
          <div class="bid-option-list">
            <article v-for="option in renderedBidOptions" :key="localize(option[0]) || option[0]">
              <h3>{{ localize(option[0]) || option[0] }}</h3>
              <p>{{ localize(option[1]) || option[1] }}</p>
              <NuxtLink class="link-more" :to="`/${lang}/inventory`">Իմանալ ավելին</NuxtLink>
            </article>
          </div>
        </div>
        <div class="laptop-mockup">
          <img src="/assets/design/products-page.png" alt="Inventory screen">
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container support-card-grid">
        <article v-for="card in renderedSupportCards" :key="localize(card[0]) || card[0]" class="support-card">
          <span class="tiny-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M12 3v18M5 10l7-7 7 7M5 14l7 7 7-7" /></svg>
          </span>
          <h3>{{ localize(card[0]) || card[0] }}</h3>
          <p>{{ localize(card[1]) || card[1] }}</p>
        </article>
      </div>
    </section>
  </main>
</template>
