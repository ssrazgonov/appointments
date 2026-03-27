<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900">Записи</h1>
      <router-link to="/appointments/create" class="btn btn-primary">
        + Новая запись
      </router-link>
    </div>

    <!-- Appointments List -->
    <div class="card">
      <div class="space-y-4">
        <div
          v-for="appointment in appointments.data"
          :key="appointment.id"
          class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
        >
          <div class="flex items-center space-x-4">
            <div>
              <p class="font-medium text-gray-900">{{ appointment.title }}</p>
              <p class="text-sm text-gray-500">
                {{ appointment.client?.full_name || appointment.client?.first_name || '—' }}
              </p>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">
              {{ formatDateTime(appointment.start_time) }}
            </span>
            <span :class="getStatusBadge(appointment.status)">
              {{ getStatusText(appointment.status) }}
            </span>
            <div class="flex space-x-2">
              <router-link
                :to="`/appointments/${appointment.id}/edit`"
                class="text-indigo-600 hover:text-indigo-900 text-sm"
              >
                Редактировать
              </router-link>
              <button
                @click="deleteAppointment(appointment.id)"
                class="text-red-600 hover:text-red-900 text-sm"
              >
                Удалить
              </button>
            </div>
          </div>
        </div>

        <p v-if="!appointments.data?.length" class="text-gray-500 text-center py-8">
          Записей нет
        </p>
      </div>

      <!-- Pagination -->
      <div v-if="appointments.last_page > 1" class="mt-4 flex justify-center space-x-2">
        <button
          v-for="page in appointments.last_page"
          :key="page"
          @click="loadPage(page)"
          :class="[
            'px-4 py-2 rounded',
            page === appointments.current_page
              ? 'bg-indigo-600 text-white'
              : 'bg-gray-200 text-gray-800 hover:bg-gray-300',
          ]"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { appointmentsApi } from '@/api/appointments';

const appointments = ref({ data: [], current_page: 1, last_page: 1 });

const loadAppointments = async (page = 1) => {
  try {
    appointments.value = await appointmentsApi.getAppointments({ page });
  } catch (error) {
    console.error('Failed to load appointments:', error);
  }
};

const loadPage = (page) => {
  loadAppointments(page);
};

const deleteAppointment = async (id) => {
  if (!confirm('Вы уверены, что хотите удалить запись?')) return;
  try {
    await appointmentsApi.deleteAppointment(id);
    loadAppointments();
  } catch (error) {
    console.error('Failed to delete appointment:', error);
    alert('Ошибка при удалении записи');
  }
};

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU', {
    day: 'numeric',
    month: 'long',
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
  loadAppointments();
});
</script>
