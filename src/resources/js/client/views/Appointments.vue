<template>
  <div class="appointments-page">
    <h1 class="page-title">История записей</h1>

    <div class="filters">
      <button
        v-for="filter in filters"
        :key="filter.value"
        @click="currentFilter = filter.value"
        :class="['filter-btn', { active: currentFilter === filter.value }]"
      >
        {{ filter.label }}
      </button>
    </div>

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
    </div>

    <div v-else-if="appointments.length" class="appointments-list">
      <div
        v-for="appointment in appointments"
        :key="appointment.id"
        class="appointment-card"
      >
        <div class="appointment-header">
          <div class="master-info">
            <div class="master-name">
              {{ appointment.user.master_profile?.display_name || appointment.user.name }}
            </div>
            <div v-if="appointment.service" class="service-name">
              {{ appointment.service.name }}
            </div>
          </div>
          <span :class="['badge', 'badge-' + getStatusType(appointment.status)]">
            {{ getStatusText(appointment.status) }}
          </span>
        </div>

        <div class="appointment-details">
          <div class="detail-row">
            <span class="icon">📅</span>
            <span>{{ formatDate(appointment.start_time) }}</span>
          </div>
          <div class="detail-row">
            <span class="icon">🕐</span>
            <span>{{ formatTime(appointment.start_time) }}</span>
          </div>
        </div>

        <div class="appointment-actions">
          <a
            v-if="appointment.user.master_profile?.slug"
            :href="`/book/${appointment.user.master_profile.slug}`"
            target="_blank"
            class="btn btn-outline btn-sm"
          >
            Повторить запись
          </a>
        </div>
      </div>

      <div v-if="hasMore" class="load-more">
        <button @click="loadMore" :disabled="loadingMore" class="btn btn-secondary">
          {{ loadingMore ? 'Загрузка...' : 'Показать ещё' }}
        </button>
      </div>
    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">📅</div>
      <h2>Нет записей</h2>
      <p v-if="currentFilter === 'all'">Вы ещё не записывались к мастерам</p>
      <p v-else>Нет записей со статусом "{{ getFilterLabel(currentFilter) }}"</p>
      <a href="/" class="btn btn-primary">Найти мастера</a>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { clientDashboardApi } from '../api/dashboard';

const filters = [
  { value: 'all', label: 'Все' },
  { value: 'scheduled', label: 'Запланированные' },
  { value: 'completed', label: 'Завершённые' },
  { value: 'cancelled', label: 'Отменённые' },
];

const currentFilter = ref('all');
const loading = ref(true);
const loadingMore = ref(false);
const appointments = ref([]);
const currentPage = ref(1);
const hasMore = ref(false);

const loadAppointments = async (page = 1, append = false) => {
  if (page === 1) {
    loading.value = true;
  } else {
    loadingMore.value = true;
  }

  try {
    const params = {
      page,
      status: currentFilter.value === 'all' ? undefined : currentFilter.value,
    };

    const data = await clientDashboardApi.getAppointmentHistory(params);
    
    if (append) {
      appointments.value = [...appointments.value, ...(data.data || [])];
    } else {
      appointments.value = data.data || [];
    }
    
    currentPage.value = page;
    hasMore.value = data.current_page < data.last_page;
  } catch (error) {
    console.error('Failed to load appointments:', error);
  } finally {
    loading.value = false;
    loadingMore.value = false;
  }
};

const loadMore = () => {
  loadAppointments(currentPage.value + 1, true);
};

watch(currentFilter, () => {
  loadAppointments(1);
});

onMounted(() => {
  loadAppointments();
});

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('ru-RU', {
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getStatusText = (status) => {
  const texts = {
    scheduled: 'Запланировано',
    confirmed: 'Подтверждено',
    completed: 'Завершено',
    cancelled: 'Отменено',
  };
  return texts[status] || status;
};

const getStatusType = (status) => {
  const types = {
    scheduled: 'info',
    confirmed: 'success',
    completed: 'success',
    cancelled: 'danger',
  };
  return types[status] || 'info';
};

const getFilterLabel = (value) => {
  return filters.find(f => f.value === value)?.label || value;
};
</script>

<style scoped>
.appointments-page {
  max-width: 800px;
  margin: 0 auto;
}

.page-title {
  font-size: 2rem;
  color: #1a202c;
  margin-bottom: 30px;
}

.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 10px 20px;
  border-radius: 8px;
  border: 2px solid #e2e8f0;
  background: white;
  color: #4a5568;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.filter-btn:hover {
  border-color: #667eea;
  color: #667eea;
}

.filter-btn.active {
  background: #667eea;
  border-color: #667eea;
  color: white;
}

.loading {
  text-align: center;
  padding: 60px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.appointments-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.appointment-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 20px;
  transition: box-shadow 0.3s;
}

.appointment-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.appointment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.master-info {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.master-name {
  font-weight: 600;
  color: #2d3748;
}

.service-name {
  font-size: 0.875rem;
  color: #718096;
}

.badge {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-info {
  background: #bee3f8;
  color: #2c5282;
}

.badge-success {
  background: #c6f6d5;
  color: #22543d;
}

.badge-danger {
  background: #fed7d7;
  color: #742a2a;
}

.appointment-details {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 15px;
}

.detail-row {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #4a5568;
}

.icon {
  font-size: 1.125rem;
}

.appointment-actions {
  display: flex;
  gap: 10px;
}

.btn {
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
  border: none;
  cursor: pointer;
  text-align: center;
  display: inline-block;
}

.btn-outline {
  background: transparent;
  color: #667eea;
  border: 2px solid #667eea;
}

.btn-outline:hover {
  background: #667eea;
  color: white;
}

.btn-secondary {
  background: #e2e8f0;
  color: #2d3748;
}

.btn-secondary:hover {
  background: #cbd5e0;
}

.btn-sm {
  padding: 8px 16px;
  font-size: 0.875rem;
}

.load-more {
  text-align: center;
  margin-top: 30px;
}

.empty-state {
  text-align: center;
  background: white;
  padding: 60px 40px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 20px;
}

.empty-state h2 {
  font-size: 1.5rem;
  color: #2d3748;
  margin-bottom: 10px;
}

.empty-state p {
  color: #718096;
  margin-bottom: 30px;
}
</style>
