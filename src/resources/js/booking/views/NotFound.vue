<template>
  <div class="not-found-page">
    <div class="not-found-container">
      <h1>404</h1>
      <p>Страница не найдена</p>
      <router-link to="/" class="btn btn-primary">
        На главную
      </router-link>
    </div>
  </div>
</template>

<script setup>
// Not Found component
</script>

<style scoped>
.not-found-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}

.not-found-container {
  max-width: 400px;
  text-align: center;
  background: white;
  padding: 60px 40px;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

h1 {
  font-size: 4rem;
  color: #e53e3e;
  margin-bottom: 20px;
}

p {
  color: #718096;
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
