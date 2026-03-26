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
import { useAuthStore } from '@/stores/auth';

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
