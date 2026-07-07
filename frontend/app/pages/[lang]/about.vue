<script setup lang="ts">
const benefits = [
  {
    title: 'Հիմնադրված 2003թ.',
    text: 'IAA Motors-ը Հայաստանում ապահովում է ԱՄՆ առաջատար աճուրդների ավտոմեքենաների ընտրություն, գնում և առաքում։',
  },
  {
    title: '7 մասնաճյուղ, 3 երկիր',
    text: 'Աշխատում ենք Հայաստանից, Վրաստանից և Ուկրաինայից՝ ապահովելով տեղական աջակցություն։',
  },
  {
    title: 'Ճանաչված մատակարար',
    text: 'Մեր թիմը տարիներ շարունակ կառուցել է վստահելի գործընկերային ցանց աճուրդների և լոգիստիկայի ոլորտում։',
  },
  {
    title: 'Խոշորագույն ավտոշուկա',
    text: 'Հասանելի են հազարավոր մեքենաներ IAAI, Copart և այլ հարթակներից։',
  },
  {
    title: '190 մասնագետ',
    text: 'Փորձառու թիմը օգնում է ընտրել, ստուգել և գնել մեքենան առանց ավելորդ ռիսկերի։',
  },
  {
    title: 'Ավելի քան 2,5 միլիոն մեքենա',
    text: 'Ամեն օր թարմացվող տեսականին տալիս է ընտրության մեծ ազատություն։',
  },
];

const team = [
  ['Կարեն Հովսեփյան', 'Տնօրեն', '/assets/extracted/01-bd0f687c8cf3.png', 'hovsepyan@platinummotors.am'],
  ['Կարեն Հովսեփյան', 'Ֆինանսական տնօրեն', '/assets/extracted/02-83d48951c018.png', 'grigor@iaa.am'],
  ['Տիգրան Պետրոսյան', 'Մաքսային բրոքեր', '/assets/extracted/01-bd0f687c8cf3.png', 'petrosyan@platinummotors.am'],
  ['Նիկոլայ Շահբազյան', 'Լոգիստիկայի մասնագետ', '/assets/extracted/02-83d48951c018.png', 'logistics@iaa.am'],
  ['Գոռ Մնացականյան', 'Մասնաճյուղի կառավարիչ', '/assets/extracted/01-bd0f687c8cf3.png'],
  ['Դավիթ Ասրյան', 'Հաճախորդների մենեջեր', '/assets/extracted/02-83d48951c018.png', 'asryan@platinummotors.am'],
  ['Հարություն Համբարձումյան', 'Վաճառքի մենեջեր', '/assets/extracted/01-bd0f687c8cf3.png', 'haxverdyan@platinummotors.am'],
  ['Ալիկ Խաչատրյան', 'Վաճառքի մենեջեր', '/assets/extracted/02-83d48951c018.png', 'alik@iaa.am'],
  ['Արմեն Հովակիմյան', 'Վաճառքի մենեջեր', '/assets/extracted/01-bd0f687c8cf3.png', 'armenia.am'],
].map(([name, role, image, email], index) => ({ name, role, image, email: email || 'info@carmark.am', phone: index % 2 ? '+374 43 578009' : '+374 96 830001' }));

const pageContent = usePageContent('about', { benefits, team });
const renderedBenefits = computed(() => pageContent.value.benefits?.length ? pageContent.value.benefits : benefits);
const renderedTeam = computed(() => pageContent.value.team?.length ? pageContent.value.team : team);
</script>

<template>
  <main class="static-page about-page">
    <section class="grid-hero">
      <div class="container">
        <h1>Մեքենայի ընտրությունից մինչև բանալիների հանձնումը</h1>
        <p>Ինչպես գտնել, ճանաչել և տեղափոխել Ձեր 100% ստուգված մեքենան՝ ապահով ու վստահելի ճանապարհով։</p>
      </div>
    </section>

    <section class="section soft about-intro">
      <div class="container">
        <p class="eyebrow">Պաշտոնական ներկայացուցիչ Հայաստանում</p>
        <h2>IAA Motors և IAA ավտոաճուրդ</h2>
        <p class="muted">Ընկերությունը հանդիսանում է Ամերիկյան խոշորագույն Insurance Auto Auction (IAA) ավտոաճուրդի պաշտոնական ներկայացուցիչ Հայաստանում։</p>

        <div class="about-benefit-grid">
          <article v-for="benefit in renderedBenefits" :key="localize(benefit.title) || benefit.title" class="about-benefit-card">
            <span class="tiny-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24"><path d="M12 3.5 20 7v5.5c0 4.4-3.1 7.1-8 8-4.9-.9-8-3.6-8-8V7l8-3.5Z" /><path d="m8.5 12 2.2 2.2L15.8 9" /></svg>
            </span>
            <h3>{{ localize(benefit.title) || benefit.title }}</h3>
            <p>{{ localize(benefit.text) || benefit.text }}</p>
          </article>
        </div>
      </div>
    </section>

    <section class="section team-section">
      <div class="container">
        <div class="center-head">
          <h2>Ծանոթացեք մեր թիմի հետ</h2>
          <p class="muted">Մենք ստեղծում ենք հստակ ու կանխատեսելի գնումների փորձ՝ յուրաքանչյուր հաճախորդի համար։</p>
        </div>

        <div class="team-grid">
          <article v-for="member in renderedTeam" :key="`${localize(member.name) || member.name}-${localize(member.role) || member.role}`" class="team-card">
            <img :src="member.image" :alt="localize(member.name) || member.name">
            <h3>{{ localize(member.name) || member.name }}</h3>
            <p>{{ localize(member.role) || member.role }}</p>
            <span>{{ member.phone }}</span>
            <span>{{ member.email }}</span>
          </article>
        </div>
      </div>
    </section>
  </main>
</template>
