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
              <th>Клиент</th>
              <th>Контакты</th>
              <th>Последний визит</th>
              <th>Всего записей</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="client in clients.data" :key="client.id || client.phone" class="client-row">
              <td>
                <div class="client-name">
                  {{ client.name || client.full_name || client.first_name || 'Анонимный клиент' }}
                </div>
              </td>
              <td>
                <div class="client-contacts">
                  <div v-if="client.phone" class="contact-item">
                    <span class="icon">📞</span>
                    <span>{{ client.phone }}</span>
                  </div>
                  <div v-if="client.email" class="contact-item">
                    <span class="icon">✉️</span>
                    <span>{{ client.email }}</span>
                  </div>
                </div>
              </td>
              <td>
                <div class="last-visit">
                  {{ formatDate(client.last_appointment || client.updated_at) }}
                </div>
              </td>
              <td>
                <div class="appointments-count">
                  <span class="badge">{{ client.total_appointments || 0 }}</span>
                  <span class="label">записей</span>
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
import { clientsApi } from '@/api/clients';

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
    const data = await clientsApi.getClients({ page, q: search });
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
      await clientsApi.updateClient(editingClient.value.id, form.value);
    } else {
      await clientsApi.createClient(form.value);
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
    await clientsApi.deleteClient(id);
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

<style scoped>
.table {
  width: 100%;
  border-collapse: collapse;
}

.table thead th {
  padding: 12px 16px;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 2px solid #e5e7eb;
}

.table tbody tr {
  border-bottom: 1px solid #f3f4f6;
  transition: background-color 0.2s;
}

.table tbody tr:hover {
  background-color: #f9fafb;
}

.table tbody td {
  padding: 16px;
  vertical-align: middle;
}

.client-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.95rem;
}

.client-contacts {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.875rem;
  color: #6b7280;
}

.contact-item .icon {
  font-size: 1rem;
}

.last-visit {
  font-size: 0.875rem;
  color: #6b7280;
}

.appointments-count {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.appointments-count .badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 32px;
  height: 32px;
  padding: 0 10px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.875rem;
}

.appointments-count .label {
  font-size: 0.875rem;
  color: #6b7280;
}

@media (max-width: 768px) {
  .table thead {
    display: none;
  }
  
  .table tbody tr {
    display: block;
    padding: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    margin-bottom: 12px;
  }
  
  .table tbody td {
    display: block;
    padding: 8px 0;
    border: none;
  }
  
  .client-contacts {
    margin-top: 8px;
  }
}
</style>
