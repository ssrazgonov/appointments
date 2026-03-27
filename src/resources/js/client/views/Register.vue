<template>
  <div class="register-page">
    <div class="register-container">
      <h1 class="page-title">Регистрация</h1>
      <p class="page-subtitle">Создайте аккаунт для отслеживания записей</p>

      <form @submit.prevent="handleRegister" class="register-form">
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
          <label>Имя</label>
          <input
            v-model="form.name"
            type="text"
            class="input"
            placeholder="Ваше имя"
          />
        </div>

        <div class="form-group">
          <label>Email</label>
          <input
            v-model="form.email"
            type="email"
            class="input"
            placeholder="email@example.com"
          />
        </div>

        <div class="form-group">
          <label>Пароль</label>
          <input
            v-model="form.password"
            type="password"
            class="input"
            placeholder="Придумайте пароль"
          />
        </div>

        <div v-if="error" class="error-message">{{ error }}</div>

        <button type="submit" :disabled="loading" class="btn btn-primary btn-block">
          {{ loading ? 'Регистрация...' : 'Зарегистрироваться' }}
        </button>

        <div class="form-footer">
          <router-link to="/client/login" class="btn btn-link">
            Уже есть аккаунт? Войти
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
  name: '',
  email: '',
  password: '',
});

const loading = ref(false);
const error = ref('');

const handleRegister = async () => {
  loading.value = true;
  error.value = '';

  const result = await authStore.register(form.value);

  if (result.success) {
    router.push('/');
  } else {
    error.value = result.message;
  }

  loading.value = false;
};
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.register-container {
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

.register-form {
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
