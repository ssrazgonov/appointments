<template>
  <div class="client-layout">
    <header class="client-header">
      <div class="container">
        <div class="header-content">
          <router-link to="/" class="logo">
            <span class="logo-icon">👤</span>
            <span class="logo-text">Личный кабинет</span>
          </router-link>

          <nav class="nav">
            <router-link to="/" class="nav-link">Записи</router-link>
            <router-link to="/masters" class="nav-link">Мастера</router-link>
            <router-link to="/profile" class="nav-link">Профиль</router-link>
          </nav>

          <div class="user-menu">
            <span class="user-name">{{ authStore.userName || 'Клиент' }}</span>
            <button @click="handleLogout" class="btn btn-text">
              Выйти
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="client-main">
      <div class="container">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { clientAuthApi } from '../api/auth';

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async () => {
  // Clear local storage immediately
  localStorage.removeItem('client_token');
  localStorage.removeItem('client_user');
  
  try {
    await clientAuthApi.logout();
  } catch (error) {
    console.error('Logout error:', error);
  }
  
  // Force redirect to login page
  window.location.href = '/client/login';
};
</script>

<style scoped>
.client-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.client-header {
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

.nav-link.router-link-active {
  color: #667eea;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 20px;
}

.user-name {
  color: #2d3748;
  font-weight: 500;
}

.btn-text {
  background: transparent;
  color: #4a5568;
  border: none;
  padding: 10px 15px;
  cursor: pointer;
  font-weight: 500;
  transition: color 0.3s;
}

.btn-text:hover {
  color: #e53e3e;
}

.client-main {
  flex: 1;
  padding: 40px 0;
}

@media (max-width: 768px) {
  .nav {
    display: none;
  }
}
</style>
