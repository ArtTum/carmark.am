export const useApiBase = () => useRuntimeConfig().public.apiBase as string;

export const apiUrl = (path: string) => `${useApiBase()}${path.startsWith('/') ? path : `/${path}`}`;

export async function adminFetch<T>(path: string, options: any = {}): Promise<T> {
  const token = import.meta.client ? localStorage.getItem('adminToken') : '';

  return await $fetch<T>(apiUrl(path), {
    ...options,
    headers: {
      ...(options.headers || {}),
      Authorization: `Bearer ${token}`,
    },
  });
}

