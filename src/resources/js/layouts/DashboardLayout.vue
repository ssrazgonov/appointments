<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <router-link to="/" class="text-xl font-bold text-indigo-600">
                RecordToMaster
              </router-link>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link
                to="/"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="
                  $route.name === 'Dashboard'
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                "
              >
                Dashboard
              </router-link>
              <router-link
                to="/clients"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="
                  $route.name === 'Clients' || $route.name === 'ClientDetail'
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                "
              >
                Клиенты
              </router-link>
              <router-link
                to="/appointments"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="
                  $route.name?.includes('Appointment')
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                "
              >
                Записи
              </router-link>
              <router-link
                to="/reports"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="
                  $route.name === 'Reports'
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                "
              >
                Отчеты
              </router-link>
              <router-link
                to="/subscription"
                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="
                  $route.name === 'Subscription'
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                "
              >
                Подписка
              </router-link>
            </div>
          </div>
          <div class="flex items-center">
            <span class="text-sm text-gray-500 mr-4">{{ authStore.userEmail }}</span>
            <button
              @click="handleLogout"
              class="text-sm text-gray-500 hover:text-gray-700"
            >
              Выйти
            </button>
          </div>
        </div>
      </div>
    </nav>

    <main class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>
