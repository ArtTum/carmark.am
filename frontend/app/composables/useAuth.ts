type AuthUser = {
  id: number;
  name: string;
  email: string;
};

const AUTH_TOKEN_KEY = 'carmarkAuthToken';
const AUTH_USER_KEY = 'carmarkAuthUser';

function authHeader(token: string) {
  return { Authorization: `Bearer ${token}` };
}

export function useAuth() {
  const token = useState<string>('auth-token', () => '');
  const user = useState<AuthUser | null>('auth-user', () => null);
  const ready = useState<boolean>('auth-ready', () => false);
  const pending = useState<boolean>('auth-pending', () => false);
  const loggedIn = computed(() => Boolean(token.value && user.value));

  function persist(nextToken: string, nextUser: AuthUser | null) {
    token.value = nextToken;
    user.value = nextUser;

    if (!import.meta.client) return;

    if (nextToken && nextUser) {
      localStorage.setItem(AUTH_TOKEN_KEY, nextToken);
      localStorage.setItem(AUTH_USER_KEY, JSON.stringify(nextUser));
      return;
    }

    localStorage.removeItem(AUTH_TOKEN_KEY);
    localStorage.removeItem(AUTH_USER_KEY);
  }

  async function initAuth() {
    if (!import.meta.client || ready.value) return;

    const storedToken = localStorage.getItem(AUTH_TOKEN_KEY) || '';
    const storedUser = localStorage.getItem(AUTH_USER_KEY);

    if (storedToken) {
      token.value = storedToken;
      try {
        user.value = storedUser ? JSON.parse(storedUser) : null;
      } catch {
        user.value = null;
      }
    }

    if (!storedToken) {
      ready.value = true;
      return;
    }

    try {
      const response = await $fetch<{ user: AuthUser }>(apiUrl('/auth/me'), {
        headers: authHeader(storedToken),
      });
      persist(storedToken, response.user);
    } catch {
      persist('', null);
    } finally {
      ready.value = true;
    }
  }

  async function login(email: string, password: string) {
    pending.value = true;
    try {
      const response = await $fetch<{ token: string; user: AuthUser }>(apiUrl('/auth/login'), {
        method: 'POST',
        body: { email, password },
      });
      persist(response.token, response.user);
      ready.value = true;
      return response.user;
    } finally {
      pending.value = false;
    }
  }

  async function register(payload: { name: string; email: string; password: string; password_confirmation: string }) {
    pending.value = true;
    try {
      const response = await $fetch<{ token: string; user: AuthUser }>(apiUrl('/auth/register'), {
        method: 'POST',
        body: payload,
      });
      persist(response.token, response.user);
      ready.value = true;
      return response.user;
    } finally {
      pending.value = false;
    }
  }

  async function updateProfile(payload: { name: string; email: string }) {
    if (!token.value) throw new Error('Unauthorized');
    const response = await $fetch<{ user: AuthUser }>(apiUrl('/auth/profile'), {
      method: 'PUT',
      headers: authHeader(token.value),
      body: payload,
    });
    persist(token.value, response.user);
    return response.user;
  }

  async function updatePassword(payload: { current_password: string; password: string; password_confirmation: string }) {
    if (!token.value) throw new Error('Unauthorized');
    const response = await $fetch<{ token: string; user: AuthUser }>(apiUrl('/auth/password'), {
      method: 'PUT',
      headers: authHeader(token.value),
      body: payload,
    });
    persist(response.token, response.user);
    return response.user;
  }

  async function logout() {
    const currentToken = token.value;
    persist('', null);
    ready.value = true;

    if (!currentToken) return;

    try {
      await $fetch(apiUrl('/auth/logout'), {
        method: 'POST',
        headers: authHeader(currentToken),
      });
    } catch {
      // Local logout is authoritative for the browser session.
    }
  }

  return {
    token,
    user,
    ready,
    pending,
    loggedIn,
    initAuth,
    login,
    register,
    updateProfile,
    updatePassword,
    logout,
  };
}
