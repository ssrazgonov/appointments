<template>
  <header class="header">
    <div class="container">
      <div class="header-content">
        <router-link to="/" class="logo">
          <span class="logo-icon">📅</span>
          <span class="logo-text">RecordToMaster</span>
        </router-link>

        <nav class="nav">
          <router-link to="/features" class="nav-link">Возможности</router-link>
          <router-link to="/pricing" class="nav-link">Тарифы</router-link>
        </nav>

        <div class="auth-buttons">
          <template v-if="isAuthenticated">
            <router-link to="/app" class="btn btn-outline">
              Личный кабинет
            </router-link>
            <button @click="handleLogout" class="btn btn-text">
              Выйти
            </button>
          </template>
          <template v-else>
            <router-link to="/app/login" class="btn btn-text">
              Войти
            </router-link>
            <router-link to="/app/register" class="btn btn-primary">
              Начать бесплатно
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { authApi } from '@/api/auth';

const router = useRouter();
const isAuthenticated = ref(false);

onMounted(() => {
  isAuthenticated.value = authApi.isAuthenticated();
});

const handleLogout = async () => {
  try {
    await authApi.logout();
    isAuthenticated.value = false;
    router.push('/');
  } catch (error) {
    console.error('Logout error:', error);
  }
};
</script>

<style scoped>
.header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 100;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  color: #1a202c;
  font-weight: 700;
  font-size: 1.25rem;
}

.logo-icon {
  font-size: 1.5rem;
}

.nav {
  display: flex;
  gap: 30px;
}

.nav-link {
  text-decoration: none;
  color: #4a5568;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-link:hover {
  color: #667eea;
}

.auth-buttons {
  display: flex;
  align-items: center;
  gap: 15px;
}

.btn {
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
  border: none;
  cursor: pointer;
  font-size: 0.875rem;
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

.btn-text {
  background: transparent;
  color: #4a5568;
  padding: 10px 15px;
}

.btn-text:hover {
  color: #667eea;
}

@media (max-width: 768px) {
  .nav {
    display: none;
  }
  
  .auth-buttons {
    gap: 10px;
  }
  
  .btn {
    padding: 8px 15px;
    font-size: 0.8rem;
  }
}
</style>
