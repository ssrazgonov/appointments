<template>
  <div class="space-y-6">
    <router-link to="/appointments" class="text-indigo-600 hover:text-indigo-900">
      ← Назад к записям
    </router-link>

    <div class="card">
      <h1 class="text-2xl font-bold text-gray-900 mb-6">
        {{ isEdit ? 'Редактировать запись' : 'Новая запись' }}
      </h1>

      <form @submit.prevent="saveAppointment" class="space-y-6">
        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Клиент *</label>
            <select v-model="form.client_id" required class="input mt-1">
              <option value="">Выберите клиента</option>
              <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.full_name || client.first_name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Название *</label>
            <input v-model="form.title" type="text" required class="input mt-1" placeholder="Например: Стрижка" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Описание</label>
          <textarea v-model="form.description" class="input mt-1" rows="3"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Дата и время начала *</label>
            <input v-model="form.start_time" type="datetime-local" required class="input mt-1" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Дата и время окончания *</label>
            <input v-model="form.end_time" type="datetime-local" required class="input mt-1" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Цена</label>
            <input v-model="form.price" type="number" step="0.01" class="input mt-1" placeholder="0" />
          </div>

          <div v-if="isEdit">
            <label class="block text-sm font-medium text-gray-700">Статус</label>
            <select v-model="form.status" class="input mt-1">
              <option value="scheduled">Запланировано</option>
              <option value="completed">Завершено</option>
              <option value="cancelled">Отменено</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Заметки</label>
          <textarea v-model="form.notes" class="input mt-1" rows="3"></textarea>
        </div>

        <div v-if="error" class="text-red-500 text-sm">
          {{ error }}
        </div>

        <div class="flex justify-end space-x-3">
          <router-link to="/appointments" class="btn btn-secondary">
            Отмена
          </router-link>
          <button type="submit" :disabled="saving" class="btn btn-primary">
            {{ saving ? 'Сохранение...' : 'Сохранить' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { appointmentService } from '@/api/appointments';
import { clientService } from '@/api/clients';

const route = useRoute();
const router = useRouter();

const isEdit = computed(() => !!route.params.id);
const clients = ref([]);
const saving = ref(false);
const error = ref('');

const form = ref({
  client_id: '',
  title: '',
  description: '',
  start_time: '',
  end_time: '',
  status: 'scheduled',
  price: '',
  notes: '',
});

const loadClients = async () => {
  try {
    const data = await clientService.getClients({ per_page: 1000 });
    clients.value = data.data || [];
  } catch (error) {
    console.error('Failed to load clients:', error);
  }
};

const loadAppointment = async () => {
  if (!isEdit.value) return;
  try {
    const appointment = await appointmentService.getAppointment(route.params.id);
    form.value = {
      client_id: appointment.client_id,
      title: appointment.title,
      description: appointment.description || '',
      start_time: formatDateTimeLocal(appointment.start_time),
      end_time: formatDateTimeLocal(appointment.end_time),
      status: appointment.status,
      price: appointment.price || '',
      notes: appointment.notes || '',
    };
  } catch (error) {
    console.error('Failed to load appointment:', error);
    alert('Ошибка при загрузке записи');
  }
};

const formatDateTimeLocal = (dateString) => {
  const date = new Date(dateString);
  const offset = date.getTimezoneOffset();
  const localDate = new Date(date.getTime() - offset * 60 * 1000);
  return localDate.toISOString().slice(0, 16);
};

const saveAppointment = async () => {
  saving.value = true;
  error.value = '';

  try {
    if (isEdit.value) {
      await appointmentService.updateAppointment(route.params.id, form.value);
    } else {
      await appointmentService.createAppointment(form.value);
    }
    router.push('/appointments');
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка при сохранении';
    console.error('Failed to save appointment:', err);
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadClients();
  loadAppointment();
});
</script>
