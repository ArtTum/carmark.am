type Lang = 'hy' | 'en' | 'ru';

const langs: Lang[] = ['hy', 'en', 'ru'];

const copy: Record<string, Record<Lang, string>> = {
  'nav.buy': { hy: 'Գնել մեքենա', en: 'Buy a car', ru: 'Купить авто' },
  'nav.auctions': { hy: 'Աճուրդներ', en: 'Auctions', ru: 'Аукционы' },
  'nav.services': { hy: 'Ծառայություններ', en: 'Services', ru: 'Услуги' },
  'nav.how': { hy: 'Ինչպես գնել', en: 'How to buy', ru: 'Как купить' },
  'nav.contact': { hy: 'Կապ մեզ հետ', en: 'Contact', ru: 'Контакты' },
  'nav.account': { hy: 'Հաշիվ', en: 'Account', ru: 'Кабинет' },
  'hero.title': { hy: 'Ապահովագրական ավտո աճուրդ', en: 'Insurance auto auctions', ru: 'Страховые автоаукционы' },
  'hero.copy': {
    hy: 'Գտեք աճուրդային մեքենաներ, հաշվարկեք վերջնական արժեքը և կառավարեք գնման ընթացքը մեկ տեղից։',
    en: 'Find auction cars, calculate the full landed cost and manage the purchase from one place.',
    ru: 'Подбирайте авто с аукционов, считайте полную стоимость и ведите покупку в одном месте.',
  },
  'search.placeholder': {
    hy: 'Որոնել մեքենաներ ըստ մակնիշի, մոդելի կամ լոտի ...',
    en: 'Search by make, model or lot ...',
    ru: 'Поиск по марке, модели или лоту ...',
  },
  'home.featured': { hy: 'Ամենապահանջված մեքենաները', en: 'Featured vehicles', ru: 'Популярные автомобили' },
  'home.auctions': { hy: 'Մոտակա և ընթացիկ աճուրդները', en: 'Upcoming auctions', ru: 'Ближайшие аукционы' },
  'home.private': { hy: 'Առկա տեսականին', en: 'Private inventory', ru: 'Авто в наличии' },
  'home.brands': { hy: 'Մեքենաների վաճառք ըստ մակնիշի', en: 'Shop by brand', ru: 'Поиск по бренду' },
  'home.services': { hy: 'Բոլոր ծառայությունները մեկ վայրում', en: 'Every service in one place', ru: 'Все услуги в одном месте' },
  'inventory.title': { hy: 'Ավտոմեքենաների կատալոգ', en: 'Inventory', ru: 'Каталог' },
  'inventory.filters': { hy: 'Ֆիլտրեր', en: 'Filters', ru: 'Фильтры' },
  'btn.search': { hy: 'Որոնել', en: 'Search', ru: 'Искать' },
  'btn.details': { hy: 'Մանրամասն', en: 'Details', ru: 'Подробнее' },
  'btn.bid': { hy: 'Գին առաջարկել', en: 'Place bid', ru: 'Сделать ставку' },
  'btn.buy': { hy: 'Գնել հիմա', en: 'Buy now', ru: 'Купить сейчас' },
  'form.name': { hy: 'Անուն', en: 'Name', ru: 'Имя' },
  'form.email': { hy: 'Էլ. հասցե', en: 'Email', ru: 'Email' },
  'form.phone': { hy: 'Հեռախոս', en: 'Phone', ru: 'Телефон' },
  'form.message': { hy: 'Հաղորդագրություն', en: 'Message', ru: 'Сообщение' },
  'form.send': { hy: 'Ուղարկել', en: 'Send', ru: 'Отправить' },
  'footer.rights': { hy: 'Բոլոր իրավունքները պաշտպանված են', en: 'All rights reserved', ru: 'Все права защищены' },
};

export function useLang() {
  const route = useRoute();
  const param = computed(() => String(route.params.lang || 'hy'));
  return computed<Lang>(() => (langs.includes(param.value as Lang) ? param.value as Lang : 'hy'));
}

export function useT() {
  const lang = useLang();
  return (key: string) => copy[key]?.[lang.value] || copy[key]?.en || key;
}

export function localize(value: any, lang = useLang().value): string {
  if (!value || typeof value !== 'object') return String(value || '');
  const preferred = value[lang];
  if (typeof preferred === 'string' && !/[ÔÕÖÐÑ]/.test(preferred)) return preferred;
  return value.en || Object.values(value).find((item) => typeof item === 'string' && !/[ÔÕÖÐÑ]/.test(item)) || '';
}

export function money(value: number | string | null | undefined): string {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
  }).format(Number(value || 0));
}

export function langPath(path = ''): string {
  const lang = useLang();
  return `/${lang.value}${path.startsWith('/') ? path : `/${path}`}`.replace(/\/$/, '');
}
