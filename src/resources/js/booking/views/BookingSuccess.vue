<template>
  <div class="success-page">
    <div class="success-container">
      <div class="success-icon">✓</div>
      <h1>Запись создана!</h1>
      <p>Вы успешно записались к мастеру.</p>
      <p class="details">
        Мы отправили подтверждение на ваши контактные данные.
      </p>
      <router-link :to="`/book/${masterSlug}`" class="btn btn-primary">
        Записаться ещё раз
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const masterSlug = computed(() => route.params.slug);
</script>

<style scoped>
.success-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}

.success-container {
  max-width: 400px;
  text-align: center;
  background: white;
  padding: 60px 40px;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.success-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 30px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  font-weight: bold;
}

h1 {
  font-size: 1.75rem;
  color: #1a202c;
  margin-bottom: 15px;
}

p {
  color: #718096;
  margin-bottom: 10px;
}

.details {
  margin-bottom: 30px;
}

.btn {
  display: inline-block;
  padding: 15px 40px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5568d3;
}
</style>
