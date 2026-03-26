<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900">Клиенты</h1>
      <button @click="showCreateModal = true" class="btn btn-primary">
        + Добавить клиента
      </button>
    </div>

    <!-- Search -->
    <div class="card">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Поиск по имени, телефону или email..."
        class="input"
        @input="handleSearch"
      />
    </div>

    <!-- Clients Table -->
    <div class="card">
      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Имя</th>
              <th>Телефон</th>
              <th>Email</th>
              <th>Последний визит</th>
              <th>Действия</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="client in clients.data" :key="client.id">
              <td>{{ client.full_name || client.first_name }}</td>
              <td>{{ client.phone || '—' }}</td>
              <td>{{ client.email || '—' }}</td>
              <td>{{ formatDate(client.updated_at) }}</td>
              <td>
                <div class="flex space-x-2">
                  <router-link
                    :to="`/clients/${client.id}`"
                    class="text-indigo-600 hover:text-indigo-900 text-sm"
                  >
                    Просмотр
                  </router-link>
                  <button
                    @click="editClient(client)"
                    class="text-blue-600 hover:text-blue-900 text-sm"
                  >
                    Редактировать
                  </button>
                  <button
                    @click="deleteClient(client.id)"
                    class="text-red-600 hover:text-red-900 text-sm"
                  >
                    Удалить
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="clients.last_page > 1" class="mt-4 flex justify-center space-x-2">
        <button
          v-for="page in clients.last_page"
          :key="page"
          @click="loadPage(page)"
          :class="[
            'px-4 py-2 rounded',
            page === clients.current_page
              ? 'bg-indigo-600 text-white'
              : 'bg-gray-200 text-gray-800 hover:bg-gray-300',
          ]"
        >
          {{ page }}
        </button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || editingClient" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">
          {{ editingClient ? 'Редактировать клиента' : 'Новый клиент' }}
        </h2>
        <form @submit.prevent="saveClient">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Имя *</label>
              <input v-model="form.first_name" type="text" required class="input mt-1" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Фамилия</label>
              <input v-model="form.last_name" type="text" class="input mt-1" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Телефон</label>
              <input v-model="form.phone" type="tel" class="input mt-1" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input v-model="form.email" type="email" class="input mt-1" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Заметки</label>
              <textarea v-model="form.notes" class="input mt-1" rows="3"></textarea>
            </div>
          </div>
          <div class="mt-6 flex justify-end space-x-3">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              Отмена
            </button>
            <button type="submit" :disabled="saving" class="btn btn-primary">
              {{ saving ? 'Сохранение...' : 'Сохранить' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { clientService } from '@/api/clients';

const clients = ref({ data: [], current_page: 1, last_page: 1 });
const searchQuery = ref('');
const showCreateModal = ref(false);
const editingClient = ref(null);
const saving = ref(false);

const form = ref({
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  notes: '',
});

const loadClients = async (page = 1, search = '') => {
  try {
    const data = await clientService.getClients({ page, q: search });
    clients.value = data;
  } catch (error) {
    console.error('Failed to load clients:', error);
  }
};

const handleSearch = () => {
  loadClients(1, searchQuery.value);
};

const loadPage = (page) => {
  loadClients(page, searchQuery.value);
};

const editClient = (client) => {
  editingClient.value = client;
  form.value = {
    first_name: client.first_name,
    last_name: client.last_name || '',
    phone: client.phone || '',
    email: client.email || '',
    notes: client.notes || '',
  };
};

const saveClient = async () => {
  saving.value = true;
  try {
    if (editingClient.value) {
      await clientService.updateClient(editingClient.value.id, form.value);
    } else {
      await clientService.createClient(form.value);
    }
    closeModal();
    loadClients();
  } catch (error) {
    console.error('Failed to save client:', error);
    alert('Ошибка при сохранении клиента');
  } finally {
    saving.value = false;
  }
};

const deleteClient = async (id) => {
  if (!confirm('Вы уверены, что хотите удалить клиента?')) return;
  try {
    await clientService.deleteClient(id);
    loadClients();
  } catch (error) {
    console.error('Failed to delete client:', error);
    alert('Ошибка при удалении клиента');
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  editingClient.value = null;
  form.value = {
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    notes: '',
  };
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU');
};

onMounted(() => {
  loadClients();
});
</script>
