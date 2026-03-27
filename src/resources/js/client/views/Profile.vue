<template>
  <div class="profile-page">
    <h1 class="page-title">Профиль</h1>

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
    </div>

    <div v-else class="profile-content">
      <div class="profile-card">
        <div class="profile-header">
          <div class="avatar-placeholder">
            {{ authStore.userName?.[0] || 'К' }}
          </div>
          <div class="profile-info">
            <h2 class="profile-name">{{ authStore.userName || 'Клиент' }}</h2>
            <p class="profile-phone">{{ authStore.userPhone }}</p>
          </div>
        </div>

        <div class="profile-stats">
          <div class="stat">
            <div class="stat-value">{{ stats.total_appointments }}</div>
            <div class="stat-label">Всего записей</div>
          </div>
          <div class="stat">
            <div class="stat-value">{{ stats.completed_count }}</div>
            <div class="stat-label">Завершённых</div>
          </div>
          <div class="stat">
            <div class="stat-value">{{ mastersCount }}</div>
            <div class="stat-label">Мастеров</div>
          </div>
        </div>

        <div class="profile-actions">
          <button @click="handleLogout" class="btn btn-danger">
            Выйти из аккаунта
          </button>
        </div>
      </div>

      <div class="help-card">
        <h3>Нужна помощь?</h3>
        <p>Если у вас возникли проблемы с записью или аккаунтом, свяжитесь с поддержкой</p>
        <a href="mailto:support@recordtomaster.ru" class="btn btn-outline">
          Написать в поддержку
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { clientDashboardApi } from '../api/dashboard';

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const stats = ref({
  total_appointments: 0,
  completed_count: 0,
});
const mastersCount = ref(0);

onMounted(async () => {
  try {
    const data = await clientDashboardApi.getDashboard();
    stats.value = data.stats;
    mastersCount.value = data.masters?.length || 0;
  } catch (error) {
    console.error('Failed to load profile data:', error);
  } finally {
    loading.value = false;
  }
});

const handleLogout = async () => {
  await authStore.logout();
  router.push('/client/login');
};
</script>

<style scoped>
.profile-page {
  max-width: 600px;
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

.profile-content {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.profile-card {
  background: white;
  border-radius: 12px;
  padding: 40px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.profile-header {
  display: flex;
  gap: 25px;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 30px;
  border-bottom: 1px solid #e2e8f0;
}

.avatar-placeholder {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 2.5rem;
  flex-shrink: 0;
}

.profile-info {
  flex: 1;
}

.profile-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0 0 5px 0;
}

.profile-phone {
  color: #718096;
  margin: 0;
}

.profile-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 30px;
}

.stat {
  text-align: center;
  padding: 20px;
  background: #f7fafc;
  border-radius: 12px;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #667eea;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 0.875rem;
  color: #718096;
}

.profile-actions {
  display: flex;
  justify-content: center;
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

.btn-danger {
  background: #fc8181;
  color: white;
}

.btn-danger:hover {
  background: #f56565;
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

.help-card {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  text-align: center;
}

.help-card h3 {
  font-size: 1.25rem;
  color: #2d3748;
  margin-bottom: 10px;
}

.help-card p {
  color: #718096;
  margin-bottom: 20px;
}

@media (max-width: 768px) {
  .profile-stats {
    grid-template-columns: 1fr;
  }

  .profile-header {
    flex-direction: column;
    text-align: center;
  }
}
</style>
