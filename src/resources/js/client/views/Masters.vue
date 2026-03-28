<template>
  <div class="masters-page">
    <h1 class="page-title">Мои мастера</h1>

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
    </div>

    <div v-else-if="masters.length" class="masters-list">
      <div
        v-for="master in masters"
        :key="master.id"
        class="master-card"
      >
        <router-link :to="`/masters/${master.id}`" class="master-link">
          <div class="master-avatar-section">
            <img
              v-if="master.avatar_url"
              :src="master.avatar_url"
              :alt="master.display_name"
              class="master-avatar"
            />
            <div v-else class="master-avatar-placeholder">
              {{ master.display_name?.[0] || 'М' }}
            </div>
          </div>

          <div class="master-info">
            <h2 class="master-name">{{ master.display_name }}</h2>
            <p v-if="master.bio" class="master-bio">{{ truncate(master.bio, 100) }}</p>
            
            <div class="master-stats">
              <div class="stat">
                <span class="stat-value">{{ master.total_visits }}</span>
                <span class="stat-label">визит(ов)</span>
              </div>
              <div class="stat">
                <span class="stat-value">{{ formatDate(master.last_visit) }}</span>
                <span class="stat-label">последний визит</span>
              </div>
            </div>

            <div v-if="master.services?.length" class="master-services">
              <span
                v-for="service in master.services.slice(0, 3)"
                :key="service.id"
                class="service-tag"
              >
                {{ service.name }}
              </span>
            </div>
          </div>

          <div class="master-action">
            <span class="btn btn-primary">Подробнее</span>
          </div>
        </router-link>
      </div>
    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">👥</div>
      <h2>Вы ещё не были ни у одного мастера</h2>
      <p>Запишитесь к мастеру через публичную страницу записи</p>
      <a href="/" class="btn btn-primary">Найти мастера</a>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { clientDashboardApi } from '../api/dashboard';

const loading = ref(true);
const masters = ref([]);

onMounted(async () => {
  try {
    const data = await clientDashboardApi.getMasters();
    masters.value = data.masters || [];
  } catch (error) {
    console.error('Failed to load masters:', error);
  } finally {
    loading.value = false;
  }
});

const truncate = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};
</script>

<style scoped>
.masters-page {
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

.masters-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.master-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  transition: box-shadow 0.3s;
}

.master-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.master-link {
  display: flex;
  padding: 25px;
  text-decoration: none;
  color: inherit;
  gap: 25px;
}

.master-avatar-section {
  flex-shrink: 0;
}

.master-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
}

.master-avatar-placeholder {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 2.5rem;
}

.master-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.master-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0;
}

.master-bio {
  color: #718096;
  line-height: 1.6;
  margin: 0;
}

.master-stats {
  display: flex;
  gap: 30px;
}

.stat {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-weight: 700;
  color: #667eea;
  font-size: 1.125rem;
}

.stat-label {
  font-size: 0.75rem;
  color: #a0aec0;
}

.master-services {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.service-tag {
  background: #edf2f7;
  color: #4a5568;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
}

.master-action {
  display: flex;
  align-items: center;
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

@media (max-width: 768px) {
  .master-link {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .master-stats {
    justify-content: center;
  }

  .master-action {
    justify-content: center;
  }
}
</style>
