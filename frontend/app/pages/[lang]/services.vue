<script setup lang="ts">
const services = [
  ['Դեպոզիտի վճարում', 'Նախատեսված վճարում աճուրդին մասնակցելու համար՝ ապահով և թափանցիկ հաշվարկով։', '/assets/home/services/deposit.svg'],
  ['Մեքենայի պատմություն', 'Նախքան մեքենան ընտրելը ստուգում ենք վնասների, վազքի և փաստաթղթերի պատմությունը։', '/assets/home/services/history.svg'],
  ['Աճուրդի մասնակցություն', 'Մասնակցում ենք աճուրդին Ձեր հաստատված բյուջեի սահմաններում և վերահսկում ենք ընթացքը։', '/assets/home/services/auction.svg'],
  ['Պայմանագրի կնքում', 'Գնումը ձևակերպվում է պայմանագրով՝ վճարման, առաքման և հանձնման հստակ փուլերով։', '/assets/home/services/contract.svg'],
  ['Ճանապարհային տեղեկատվություններ', 'Հաշվարկում ենք ճանապարհը, նավահանգիստը, տեղափոխման ժամանակը և հնարավոր ծախսերը։', '/assets/home/services/deposit.svg'],
  ['Ապահովագրություն', 'Առաջարկում ենք ապահովագրական լուծումներ մեքենայի տեղափոխման փուլերի համար։', '/assets/home/services/history.svg'],
  ['Ծովային տեղափոխություններ', 'Կազմակերպում ենք մեքենայի տեղափոխումը ԱՄՆ նավահանգստից մինչև տարածաշրջան։', '/assets/home/services/auction.svg'],
  ['Մաքսազերծում', 'Օգնում ենք մաքսային հաշվարկների, փաստաթղթերի և տեղական ձևակերպումների հարցերում։', '/assets/home/services/contract.svg'],
  ['Հանձնում', 'Վերջնական փուլում կազմակերպում ենք մեքենայի ստացումը և փոխանցումը հաճախորդին։', '/assets/home/services/deposit.svg'],
].map(([title, text, icon], index) => ({ title, text, icon, flip: index % 2 === 1 }));

const pageContent = usePageContent('services', { services });
const renderedServices = computed(() => pageContent.value.services?.length ? pageContent.value.services : services);
</script>

<template>
  <main class="static-page services-page">
    <section class="grid-hero">
      <div class="container">
        <h1 v-if="localize(pageContent.hero?.title)">{{ localize(pageContent.hero.title) }}</h1>
        <h1 v-else>Ավտոմեքենայի ճիշտ ընտրություն</h1>
        <p>Աշխատում ենք գնման բոլոր փուլերում՝ մեքենայի ընտրությունից մինչև առաքում և հանձնում։</p>
      </div>
    </section>

    <section class="section services-timeline">
      <div class="container">
        <article v-for="service in renderedServices" :key="localize(service.title) || service.title" class="service-row" :class="{ flip: service.flip }">
          <div class="service-illustration" aria-hidden="true">
            <span class="illustration-paper">
              <img :src="service.icon" :alt="localize(service.title) || service.title">
            </span>
          </div>
          <div class="service-row-copy">
            <h2>{{ localize(service.title) || service.title }}</h2>
            <p>{{ localize(service.text) || service.text }}</p>
          </div>
        </article>
      </div>
    </section>
  </main>
</template>
