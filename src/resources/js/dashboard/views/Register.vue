<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Регистрация
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
            Уже есть аккаунт? Войти
          </router-link>
        </p>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Имя</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="input mt-1"
              placeholder="Ваше имя"
            />
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="input mt-1"
              placeholder="Email"
            />
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              minlength="8"
              class="input mt-1"
              placeholder="Пароль"
            />
          </div>
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Подтверждение пароля
            </label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              minlength="8"
              class="input mt-1"
              placeholder="Подтвердите пароль"
            />
          </div>
          <div>
            <label for="business_name" class="block text-sm font-medium text-gray-700">
              Название бизнеса
            </label>
            <input
              id="business_name"
              v-model="form.business_name"
              type="text"
              class="input mt-1"
              placeholder="Например: Салон красоты"
            />
          </div>
        </div>

        <div v-if="error" class="text-red-500 text-sm text-center">
          {{ error }}
        </div>

        <div>
          <button type="submit" :disabled="loading" class="btn btn-primary w-full">
            {{ loading ? 'Регистрация...' : 'Зарегистрироваться' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/dashboard/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  business_name: '',
});

const loading = ref(false);
const error = ref('');

const handleRegister = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Пароли не совпадают';
    return;
  }

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
.input {
  width: 100%;
  padding: 16px 20px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s;
  background: #f7fafc;
}

.input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.input::placeholder {
  color: #a0aec0;
}

.btn {
  padding: 16px 30px;
  border-radius: 12px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
  display: inline-block;
  text-align: center;
  font-size: 1rem;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.btn-primary:disabled {
  background: #cbd5e0;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.w-full {
  width: 100%;
}

.space-y-4 > * + * {
  margin-top: 1rem;
}

.space-y-6 > * + * {
  margin-top: 1.5rem;
}

.text-red-500 {
  color: #e53e3e;
}

.text-sm {
  font-size: 0.875rem;
}

.text-center {
  text-align: center;
}

.mt-2 {
  margin-top: 0.5rem;
}

.mt-4 {
  margin-top: 1rem;
}

.mt-6 {
  margin-top: 1.5rem;
}

.mt-8 {
  margin-top: 2rem;
}

.text-xs {
  font-size: 0.75rem;
}

.text-gray-500 {
  color: #718096;
}

.text-gray-600 {
  color: #4a5568;
}

.text-gray-700 {
  color: #4a5568;
}

.text-gray-900 {
  color: #1a202c;
}

.font-medium {
  font-weight: 500;
}

.text-indigo-600 {
  color: #667eea;
}

.text-indigo-600:hover {
  color: #5568d3;
}

.block {
  display: block;
}

.max-w-md {
  max-width: 28rem;
}

.w-full {
  width: 100%;
}

.space-y-8 > * + * {
  margin-top: 2rem;
}

.bg-gray-50 {
  background-color: #f7fafc;
}

.min-h-screen {
  min-height: 100vh;
}

.flex {
  display: flex;
}

.items-center {
  align-items: center;
}

.justify-center {
  justify-content: center;
}

.py-12 {
  padding-top: 3rem;
  padding-bottom: 3rem;
}

.px-4 {
  padding-left: 1rem;
  padding-right: 1rem;
}

@media (max-width: 640px) {
  .px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .sm\:px-6 {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
  
  .lg\:px-8 {
    padding-left: 2rem;
    padding-right: 2rem;
  }
  
  .text-3xl {
    font-size: 1.5rem;
  }
  
  .input {
    padding: 14px 16px;
    font-size: 0.95rem;
  }
  
  .btn {
    padding: 14px 24px;
    font-size: 0.95rem;
  }
}
</style>
