<template>
  <div class="login-page">
    <div class="login-container">
      <h1 class="page-title">Вход для клиентов</h1>
      <p class="page-subtitle">Войдите чтобы увидеть историю записей</p>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label>Телефон *</label>
          <input
            v-model="form.phone"
            type="tel"
            required
            class="input"
            placeholder="+7 (___) ___-__-__"
          />
        </div>

        <div class="form-group">
          <label>Пароль</label>
          <input
            v-model="form.password"
            type="password"
            class="input"
            placeholder="Введите пароль"
          />
        </div>

        <div v-if="showVerification" class="form-group">
          <label>Код подтверждения</label>
          <input
            v-model="form.verification_code"
            type="text"
            class="input"
            placeholder="000000"
            maxlength="6"
          />
        </div>

        <div v-if="error" class="error-message">{{ error }}</div>

        <button type="submit" :disabled="loading" class="btn btn-primary btn-block">
          {{ loading ? 'Вход...' : 'Войти' }}
        </button>

        <div class="form-footer">
          <button type="button" @click="sendCode" :disabled="loading" class="btn btn-link">
            Получить код по SMS
          </button>
          <router-link to="/client/register" class="btn btn-link">
            Нет аккаунта? Зарегистрироваться
          </router-link>
        </div>
      </form>

      <div class="back-link">
        <router-link to="/">← На главную</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  phone: '',
  password: '',
  verification_code: '',
});

const loading = ref(false);
const error = ref('');
const showVerification = ref(false);

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  const result = await authStore.login(form.value);

  if (result.success) {
    router.push('/');
  } else {
    error.value = result.message;
    if (result.message?.includes('verification')) {
      showVerification.value = true;
    }
  }

  loading.value = false;
};

const sendCode = async () => {
  if (!form.value.phone) {
    error.value = 'Введите номер телефона';
    return;
  }

  loading.value = true;
  error.value = '';

  try {
    const response = await fetch('/api/client/send-verification-code', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ phone: form.value.phone }),
    });

    const data = await response.json();
    
    if (response.ok) {
      showVerification.value = true;
      error.value = `Код: ${data.code}`; // For testing only
    } else {
      error.value = data.message || 'Ошибка отправки кода';
    }
  } catch (err) {
    error.value = 'Ошибка сети';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-container {
  max-width: 450px;
  width: 100%;
  background: white;
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.page-title {
  text-align: center;
  font-size: 1.75rem;
  color: #1a202c;
  margin-bottom: 10px;
}

.page-subtitle {
  text-align: center;
  color: #718096;
  margin-bottom: 30px;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 500;
  color: #2d3748;
}

.input {
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.input:focus {
  outline: none;
  border-color: #667eea;
}

.error-message {
  color: #e53e3e;
  background: #fff5f5;
  padding: 12px;
  border-radius: 8px;
  font-size: 0.875rem;
}

.btn {
  padding: 12px 30px;
  border-radius: 8px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
  display: inline-block;
  text-align: center;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5568d3;
}

.btn-primary:disabled {
  background: #a0aec0;
  cursor: not-allowed;
}

.btn-block {
  width: 100%;
}

.btn-link {
  background: transparent;
  color: #667eea;
  padding: 8px 15px;
  font-size: 0.875rem;
}

.btn-link:hover {
  text-decoration: underline;
}

.form-footer {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: center;
}

.back-link {
  text-align: center;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

.back-link a {
  color: #667eea;
  text-decoration: none;
}

.back-link a:hover {
  text-decoration: underline;
}
</style>
