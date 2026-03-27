<template>
  <div class="master-profile-page">
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
    </div>

    <div v-else-if="master" class="profile-content">
      <router-link to="/client/masters" class="back-link">← Назад к мастерам</router-link>

      <div class="master-header">
        <img
          v-if="master.avatar_url"
          :src="master.avatar_url"
          :alt="master.display_name"
          class="master-avatar"
        />
        <div v-else class="master-avatar-placeholder">
          {{ master.display_name?.[0] || 'М' }}
        </div>
        
        <div class="master-details">
          <h1 class="master-name">{{ master.display_name }}</h1>
          <p v-if="master.bio" class="master-bio">{{ master.bio }}</p>
        </div>
      </div>

      <div class="action-section">
        <a v-if="master.slug" :href="`/book/${master.slug}`" target="_blank" class="btn btn-primary btn-lg">
          Записаться онлайн
        </a>
      </div>

      <section class="section">
        <h2 class="section-title">История записей</h2>
        <div v-if="appointments.length" class="appointments-list">
          <div
            v-for="appointment in appointments"
            :key="appointment.id"
            class="appointment-card"
          >
            <div class="appointment-date">
              {{ formatDateTime(appointment.start_time) }}
            </div>
            <div class="appointment-service">
              {{ appointment.service?.name || 'Услуга' }}
            </div>
            <span :class="['badge', 'badge-' + getStatusType(appointment.status)]">
              {{ getStatusText(appointment.status) }}
            </span>
          </div>
        </div>
        <p v-else class="empty-message">История записей пуста</p>
      </section>
    </div>

    <div v-else class="error-state">
      <h2>Мастер не найден</h2>
      <router-link to="/client/masters" class="btn btn-primary">К мастерам</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { clientDashboardApi } from '../api/dashboard';

const route = useRoute();
const loading = ref(true);
const master = ref(null);
const appointments = ref([]);

const allMasters = ref([]);

onMounted(async () => {
  try {
    const data = await clientDashboardApi.getMasters();
    allMasters.value = data.masters || [];
    
    const foundMaster = allMasters.value.find(m => m.id === parseInt(route.params.id));
    if (foundMaster) {
      master.value = foundMaster;
      
      // Get appointments for this master
      const historyData = await clientDashboardApi.getAppointmentHistory();
      appointments.value = historyData.data?.filter(a => a.user_id === foundMaster.id) || [];
    }
  } catch (error) {
    console.error('Failed to load master profile:', error);
  } finally {
    loading.value = false;
  }
});

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getStatusText = (status) => {
  const texts = {
    scheduled: 'Запланировано',
    completed: 'Завершено',
    cancelled: 'Отменено',
  };
  return texts[status] || status;
};

const getStatusType = (status) => {
  const types = {
    scheduled: 'info',
    completed: 'success',
    cancelled: 'danger',
  };
  return types[status] || 'info';
};
</script>

<style scoped>
.master-profile-page {
  max-width: 800px;
  margin: 0 auto;
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

.back-link {
  display: inline-block;
  color: #667eea;
  text-decoration: none;
  margin-bottom: 20px;
  font-weight: 500;
}

.back-link:hover {
  text-decoration: underline;
}

.profile-content {
  background: white;
  border-radius: 12px;
  padding: 40px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.master-header {
  display: flex;
  gap: 30px;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 30px;
  border-bottom: 1px solid #e2e8f0;
}

.master-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.master-avatar-placeholder {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 3rem;
  flex-shrink: 0;
}

.master-details {
  flex: 1;
}

.master-name {
  font-size: 2rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0 0 15px 0;
}

.master-bio {
  color: #4a5568;
  line-height: 1.8;
  margin: 0;
}

.action-section {
  margin-bottom: 30px;
}

.btn {
  padding: 12px 30px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
  border: none;
  cursor: pointer;
  text-align: center;
  display: inline-block;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5568d3;
}

.btn-lg {
  padding: 15px 40px;
  font-size: 1.125rem;
}

.section {
  margin-top: 30px;
}

.section-title {
  font-size: 1.25rem;
  color: #2d3748;
  margin-bottom: 20px;
}

.appointments-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.appointment-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background: #f7fafc;
  border-radius: 8px;
}

.appointment-date {
  font-weight: 600;
  color: #2d3748;
}

.appointment-service {
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

.empty-message {
  text-align: center;
  color: #718096;
  padding: 40px;
}

.error-state {
  text-align: center;
  background: white;
  padding: 60px 40px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.error-state h2 {
  color: #2d3748;
  margin-bottom: 30px;
}

@media (max-width: 768px) {
  .master-header {
    flex-direction: column;
    text-align: center;
  }

  .appointment-card {
    flex-direction: column;
    gap: 10px;
    text-align: center;
  }
}
</style>
