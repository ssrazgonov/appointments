<template>
  <div class="dashboard-page">
    <h1 class="page-title">Мои записи</h1>

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
    </div>

    <div v-else class="dashboard-content">
      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-value">{{ stats.total_appointments }}</div>
          <div class="stat-label">Всего записей</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.upcoming_count }}</div>
          <div class="stat-label">Предстоящие</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.completed_count }}</div>
          <div class="stat-label">Завершённые</div>
        </div>
      </div>

      <!-- Upcoming Appointments -->
      <section class="section">
        <h2 class="section-title">Предстоящие записи</h2>
        <div v-if="appointments.upcoming?.length" class="appointments-list">
          <div
            v-for="appointment in appointments.upcoming"
            :key="appointment.id"
            class="appointment-card"
          >
            <div class="appointment-header">
              <div class="master-info">
                <img
                  v-if="appointment.user.master_profile?.avatar_url"
                  :src="appointment.user.master_profile.avatar_url"
                  :alt="appointment.user.master_profile.display_name"
                  class="master-avatar"
                />
                <div v-else class="master-avatar-placeholder">
                  {{ appointment.user.master_profile?.display_name?.[0] || 'М' }}
                </div>
                <div class="master-name">
                  {{ appointment.user.master_profile?.display_name || appointment.user.name }}
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
              <div v-if="appointment.service" class="detail-row">
                <span class="icon">✂️</span>
                <span>{{ appointment.service.name }}</span>
              </div>
            </div>

            <div class="appointment-actions">
              <router-link
                :to="`/client/masters/${appointment.user_id}`"
                class="btn btn-outline btn-sm"
              >
                К мастеру
              </router-link>
              <button @click="repeatBooking(appointment)" class="btn btn-primary btn-sm">
                Повторить
              </button>
            </div>
          </div>
        </div>
        <p v-else class="empty-message">Нет предстоящих записей</p>
      </section>

      <!-- Recent Masters -->
      <section class="section">
        <h2 class="section-title">
          <router-link to="/client/masters" class="section-link">
            Мои мастера
          </router-link>
        </h2>
        <div v-if="masters.length" class="masters-grid">
          <div
            v-for="master in masters.slice(0, 3)"
            :key="master.id"
            class="master-card"
          >
            <router-link :to="`/client/masters/${master.id}`" class="master-link">
              <img
                v-if="master.avatar_url"
                :src="master.avatar_url"
                :alt="master.name"
                class="master-card-avatar"
              />
              <div v-else class="master-card-avatar-placeholder">
                {{ master.name?.[0] || 'М' }}
              </div>
              <div class="master-card-info">
                <h3 class="master-card-name">{{ master.name }}</h3>
                <p class="master-card-visits">{{ master.total_visits }} визит(ов)</p>
              </div>
            </router-link>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { clientDashboardApi } from '../api/dashboard';

const router = useRouter();

const loading = ref(true);
const appointments = ref({
  all: [],
  upcoming: [],
  completed: [],
  cancelled: [],
});
const masters = ref([]);
const stats = ref({
  total_appointments: 0,
  upcoming_count: 0,
  completed_count: 0,
});

onMounted(async () => {
  try {
    const data = await clientDashboardApi.getDashboard();
    appointments.value = data.appointments;
    masters.value = data.masters;
    stats.value = data.stats;
  } catch (error) {
    console.error('Failed to load dashboard:', error);
  } finally {
    loading.value = false;
  }
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

const repeatBooking = (appointment) => {
  const masterSlug = appointment.user.master_profile?.slug;
  if (masterSlug) {
    window.open(`/book/${masterSlug}`, '_blank');
  }
};
</script>

<style scoped>
.dashboard-page {
  max-width: 1000px;
  margin: 0 auto;
}

.page-title {
  font-size: 2rem;
  color: #1a202c;
  margin-bottom: 30px;
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

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  text-align: center;
}

.stat-value {
  font-size: 2.5rem;
  font-weight: bold;
  color: #667eea;
  margin-bottom: 5px;
}

.stat-label {
  color: #718096;
  font-size: 0.875rem;
}

.section {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  margin-bottom: 30px;
}

.section-title {
  font-size: 1.25rem;
  color: #2d3748;
  margin-bottom: 20px;
}

.section-link {
  color: inherit;
  text-decoration: none;
}

.section-link:hover {
  color: #667eea;
}

.appointments-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.appointment-card {
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
  align-items: center;
  gap: 12px;
}

.master-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.master-avatar-placeholder {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.25rem;
}

.master-name {
  font-weight: 600;
  color: #2d3748;
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
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5568d3;
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

.btn-sm {
  padding: 8px 16px;
  font-size: 0.875rem;
}

.masters-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.master-card {
  background: #f7fafc;
  border-radius: 12px;
  overflow: hidden;
}

.master-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 25px;
  text-decoration: none;
  color: inherit;
  transition: background 0.3s;
}

.master-link:hover {
  background: #edf2f7;
}

.master-card-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 15px;
}

.master-card-avatar-placeholder {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 2rem;
  margin-bottom: 15px;
}

.master-card-name {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 5px;
  text-align: center;
}

.master-card-visits {
  font-size: 0.875rem;
  color: #718096;
  text-align: center;
}

.empty-message {
  text-align: center;
  color: #718096;
  padding: 40px;
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .masters-grid {
    grid-template-columns: 1fr;
  }

  .appointment-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}
</style>
