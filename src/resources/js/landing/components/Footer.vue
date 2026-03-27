<template>
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>RecordToMaster</h3>
          <p>CRM система для мастеров красоты и частных специалистов</p>
        </div>

        <div class="footer-section">
          <h4>Продукт</h4>
          <router-link to="/features">Возможности</router-link>
          <router-link to="/pricing">Тарифы</router-link>
        </div>

        <div class="footer-section">
          <h4>Компания</h4>
          <a href="mailto:support@recordtomaster.ru">support@recordtomaster.ru</a>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; {{ new Date().getFullYear() }} RecordToMaster. Все права защищены.</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
// Footer component
</script>

<style scoped>
.footer {
  background: #1a202c;
  color: white;
  padding: 60px 20px 30px;
  margin-top: auto;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-content {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 40px;
  margin-bottom: 40px;
}

.footer-section h3 {
  font-size: 1.25rem;
  margin-bottom: 15px;
}

.footer-section h4 {
  font-size: 1rem;
  margin-bottom: 15px;
  color: #a0aec0;
}

.footer-section p {
  color: #a0aec0;
  line-height: 1.6;
}

.footer-section a {
  display: block;
  color: #a0aec0;
  text-decoration: none;
  margin-bottom: 10px;
  transition: color 0.3s;
}

.footer-section a:hover {
  color: white;
}

.footer-bottom {
  border-top: 1px solid #2d3748;
  padding-top: 30px;
  text-align: center;
  color: #718096;
}

@media (max-width: 768px) {
  .footer-content {
    grid-template-columns: 1fr;
    gap: 30px;
  }
}
</style>
