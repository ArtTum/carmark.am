type SitePage = {
  id?: number;
  slug: string;
  title?: Record<string, string>;
  body?: any;
};

type SiteContent = {
  settings: Record<string, any>;
  pages: SitePage[];
};

function isPlainObject(value: unknown): value is Record<string, any> {
  return Boolean(value) && typeof value === 'object' && !Array.isArray(value);
}

function mergeContent<T>(fallback: T, override: unknown): T {
  if (Array.isArray(override)) return Array.isArray(fallback) ? override as T : fallback;
  if (!isPlainObject(fallback) || !isPlainObject(override)) {
    return (override ?? fallback) as T;
  }

  const merged: Record<string, any> = { ...fallback };

  Object.entries(override).forEach(([key, value]) => {
    if (value === null || value === undefined || value === '') return;
    if (Array.isArray(value)) {
      merged[key] = value;
      return;
    }

    merged[key] = isPlainObject(value) && isPlainObject(merged[key])
      ? mergeContent(merged[key], value)
      : value;
  });

  return merged as T;
}

export async function useSiteContent() {
  return await useAsyncData<SiteContent>('site-content', () => $fetch(apiUrl('/site')), {
    default: () => ({ settings: {}, pages: [] }),
  });
}

export function useSiteSettings() {
  const { data } = useNuxtData<SiteContent>('site-content');
  return computed(() => data.value?.settings || {});
}

export function useSitePages() {
  const { data } = useNuxtData<SiteContent>('site-content');
  return computed(() => data.value?.pages || []);
}

export function useSitePage(slug: string) {
  const pages = useSitePages();
  return computed(() => pages.value.find((page) => page.slug === slug) || null);
}

export function usePageContent<T extends Record<string, any>>(slug: string, fallback: T) {
  const page = useSitePage(slug);
  return computed(() => mergeContent(fallback, page.value?.body));
}

export function usePageTitle(slug: string, fallback = '') {
  const page = useSitePage(slug);
  return computed(() => localize(page.value?.title) || fallback);
}

export function siteContentValue<T>(value: T | undefined, fallback: T): T {
  if (value === null || value === undefined || value === '') return fallback;
  return value;
}
