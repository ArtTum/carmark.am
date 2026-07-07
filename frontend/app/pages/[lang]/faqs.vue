<script setup lang="ts">
const questions = [
  ['Ինչպե՞ս է հաշվարկվում մեքենայի վերջնական արժեքը', 'Վերջնական արժեքը կազմվում է մեքենայի գնից, աճուրդային վճարներից, տեղափոխումից, մաքսազերծումից և ծառայության վճարից։'],
  ['Կարո՞ղ եմ գնել առանց աճուրդի մասնակցելու', 'Այո, առկա տեսականու և “Գնել հիմա” տարբերակների դեպքում կարող եք ընտրել մեքենան հաստատված գնով։'],
  ['Որքա՞ն է տևում առաքումը', 'Ժամկետը կախված է նահանգից, նավահանգստից և տեղական ձևակերպումներից։ Մեր թիմը նախապես տալիս է մոտավոր ժամանակացույց։'],
  ['Ինչպե՞ս է պահվում իմ հայտը', 'Հայտը գրանցվում է համակարգում, իսկ մասնագետը կապվում է Ձեզ հետ հաջորդ քայլերը հաստատելու համար։'],
];

const pageContent = usePageContent('faqs', { questions });
const renderedQuestions = computed(() => pageContent.value.questions?.length ? pageContent.value.questions : questions);
</script>

<template>
  <main class="static-page">
    <section class="grid-hero">
      <div class="container">
        <h1>FAQs</h1>
        <p>Ամենահաճախ տրվող հարցերը մեքենա ընտրելու, գնելու և առաքելու մասին։</p>
      </div>
    </section>

    <section class="section">
      <div class="container faq-list">
        <article v-for="item in renderedQuestions" :key="localize(item[0]) || item[0]" class="profile-card">
          <h2>{{ localize(item[0]) || item[0] }}</h2>
          <p class="muted">{{ localize(item[1]) || item[1] }}</p>
        </article>
      </div>
    </section>
  </main>
</template>
