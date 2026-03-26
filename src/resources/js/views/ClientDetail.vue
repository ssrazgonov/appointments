<template>
  <div class="space-y-6">
    <router-link to="/clients" class="text-indigo-600 hover:text-indigo-900">
      ← Назад к клиентам
    </router-link>

    <div v-if="client" class="card">
      <h1 class="text-2xl font-bold text-gray-900 mb-6">
        {{ client.full_name || client.first_name }}
      </h1>

      <div class="grid grid-cols-2 gap-6">
        <div>
          <h3 class="text-sm font-medium text-gray-500">Телефон</h3>
          <p class="mt-1 text-gray-900">{{ client.phone || '—' }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Email</h3>
          <p class="mt-1 text-gray-900">{{ client.email || '—' }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Дата рождения</h3>
          <p class="mt-1 text-gray-900">{{ client.birth_date || '—' }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Заметки</h3>
          <p class="mt-1 text-gray-900">{{ client.notes || '—' }}</p>
        </div>
      </div>
    </div>

    <div class="card">
      <h2 class="text-lg font-medium text-gray-900 mb-4">История записей</h2>
      <div v-if="client?.appointments?.length" class="space-y-3">
        <div
          v-for="appointment in client.appointments"
          :key="appointment.id"
          class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
        >
          <div>
            <p class="font-medium text-gray-900">{{ appointment.title }}</p>
            <p class="text-sm text-gray-500">{{ formatDate(appointment.start_time) }}</p>
          </div>
          <span :class="getStatusBadge(appointment.status)">
            {{ getStatusText(appointment.status) }}
          </span>
        </div>
      </div>
      <p v-else class="text-gray-500 text-center py-8">
        Записей нет
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { clientService } from '@/api/clients';

const route = useRoute();
const client = ref(null);

const fetchClient = async () => {
  try {
    client.value = await clientService.getClient(route.params.id);
  } catch (error) {
    console.error('Failed to fetch client:', error);
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getStatusBadge = (status) => {
  const badges = {
    scheduled: 'badge-info',
    completed: 'badge-success',
    cancelled: 'badge-danger',
  };
  return `badge ${badges[status] || 'badge-info'}`;
};

const getStatusText = (status) => {
  const texts = {
    scheduled: 'Запланировано',
    completed: 'Завершено',
    cancelled: 'Отменено',
  };
  return texts[status] || status;
};

onMounted(() => {
  fetchClient();
});
</script>
