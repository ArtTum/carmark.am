type Lang = 'hy' | 'en' | 'ru';

const langs: Lang[] = ['hy', 'en', 'ru'];

const copy: Record<string, Record<Lang, string>> = {
  'nav.buy': { hy: 'Գնել մեքենա', en: 'Buy a car', ru: 'Купить авто' },
  'nav.auctions': { hy: 'Աճուրդներ', en: 'Auctions', ru: 'Аукционы' },
  'nav.company': { hy: 'Ընկերություն', en: 'Company', ru: 'Компания' },
  'nav.inventory': { hy: 'Առկա տեսականի', en: 'Inventory', ru: 'Авто в наличии' },
  'nav.services': { hy: 'Ծառայություններ', en: 'Services', ru: 'Услуги' },
  'nav.how': { hy: 'Ինչպես գնել', en: 'How to buy', ru: 'Как купить' },
  'nav.contact': { hy: 'Կապ մեզ հետ', en: 'Contact', ru: 'Контакты' },
  'nav.about': { hy: 'Մեր մասին', en: 'About us', ru: 'О нас' },
  'nav.account': { hy: 'Հաշիվ', en: 'Account', ru: 'Кабинет' },
  'nav.calculator': { hy: 'Հաշվիչ', en: 'Calculator', ru: 'Калькулятор' },

  'hero.title': { hy: 'Ապահովագրական Ավտո Աճուրդ', en: 'Insurance Auto Auctions', ru: 'Страховые автоаукционы' },
  'hero.copy': {
    hy: 'Ապահովագրական աճուրդների մեքենաներ՝ աջակցությամբ գնման, հաշվարկի և առաքման բոլոր փուլերում։',
    en: 'Auction cars with purchase, estimate and delivery support from the first search to the final handoff.',
    ru: 'Авто со страховых аукционов с сопровождением покупки, расчета и доставки.',
  },
  'search.placeholder': {
    hy: 'Որոնել մեքենաներ ըստ մակնիշի, մոդելի ...',
    en: 'Search by make, model or lot ...',
    ru: 'Поиск по марке, модели или лоту ...',
  },

  'home.featured': { hy: 'Ամենապահանջված մեքենաները', en: 'Featured vehicles', ru: 'Популярные автомобили' },
  'home.auctions': { hy: 'Մոտակա և ընթացիկ աճուրդները', en: 'Upcoming auctions', ru: 'Ближайшие аукционы' },
  'home.private': { hy: 'Առկա տեսականի', en: 'Private inventory', ru: 'Авто в наличии' },
  'home.brands': { hy: 'Մեքենաների վաճառք ըստ մակնիշի', en: 'Shop by brand', ru: 'Поиск по бренду' },
  'home.services': { hy: 'Բոլոր ծառայությունները մեկ վայրում', en: 'Every service in one place', ru: 'Все услуги в одном месте' },

  'inventory.title': { hy: 'Առաջարկվող ֆիլտրեր', en: 'Available vehicles', ru: 'Доступные автомобили' },
  'inventory.filters': { hy: 'Ֆիլտրեր', en: 'Filters', ru: 'Фильтры' },
  'inventory.results': { hy: 'Որոնման արդյունքներ', en: 'Search results', ru: 'Результаты поиска' },

  'btn.search': { hy: 'Որոնել', en: 'Search', ru: 'Искать' },
  'btn.details': { hy: 'Տեսնել', en: 'View', ru: 'Смотреть' },
  'btn.bid': { hy: 'Գին առաջարկել', en: 'Place bid', ru: 'Сделать ставку' },
  'btn.buy': { hy: 'Գնել հիմա', en: 'Buy now', ru: 'Купить сейчас' },
  'btn.save': { hy: 'Պահպանել', en: 'Save', ru: 'Сохранить' },
  'btn.send': { hy: 'Ուղարկել', en: 'Send', ru: 'Отправить' },
  'btn.more': { hy: 'Իմանալ ավելին', en: 'Learn more', ru: 'Подробнее' },

  'form.name': { hy: 'Անուն', en: 'Name', ru: 'Имя' },
  'form.lastName': { hy: 'Ազգանուն', en: 'Last name', ru: 'Фамилия' },
  'form.email': { hy: 'Էլ. հասցե', en: 'Email', ru: 'Email' },
  'form.phone': { hy: 'Հեռախոսահամար', en: 'Phone', ru: 'Телефон' },
  'form.message': { hy: 'Նամակ', en: 'Message', ru: 'Сообщение' },
  'form.send': { hy: 'Ուղարկել', en: 'Send', ru: 'Отправить' },
  'form.success': { hy: 'Հայտը ուղարկված է։', en: 'Request sent.', ru: 'Заявка отправлена.' },

  'footer.rights': { hy: 'Բոլոր իրավունքները պաշտպանված են', en: 'All rights reserved', ru: 'Все права защищены' },
};

function hasMojibake(value: string) {
  return /[ÃÔÕÖÐÑ€]/.test(value);
}

export function useLang() {
  const route = useRoute();
  const param = computed(() => String(route.params.lang || 'hy'));
  return computed<Lang>(() => (langs.includes(param.value as Lang) ? param.value as Lang : 'hy'));
}

export function useT() {
  const lang = useLang();
  const { data } = useNuxtData<any>('site-content');

  return (key: string) => {
    const backendCopy = data.value?.settings?.copy?.[key];
    const backendValue = localize(backendCopy, lang.value);
    return backendValue || copy[key]?.[lang.value] || copy[key]?.en || key;
  };
}

export function localize(value: any, lang = useLang().value): string {
  if (!value || typeof value !== 'object') return String(value || '');

  const preferred = value[lang];
  if (typeof preferred === 'string' && preferred.trim() && !hasMojibake(preferred)) return preferred;

  const english = value.en;
  if (typeof english === 'string' && english.trim() && !hasMojibake(english)) return english;

  return String(Object.values(value).find((item) => typeof item === 'string' && item.trim() && !hasMojibake(item)) || '');
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
