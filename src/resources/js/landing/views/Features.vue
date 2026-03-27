<template>
  <div class="features-page">
    <div class="container">
      <h1 class="page-title">Возможности RecordToMaster</h1>
      <p class="page-subtitle">
        Всё необходимое для управления вашим бизнесом в одной системе
      </p>

      <div class="features-list">
        <div class="feature-item">
          <div class="feature-icon">📅</div>
          <div class="feature-content">
            <h3>Умный календарь записей</h3>
            <p>
              Планируйте записи с помощью интуитивного календаря. 
              Перетаскивайте события, устанавливайте повторения и получайте уведомления.
            </p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon">👥</div>
          <div class="feature-content">
            <h3>База клиентов</h3>
            <p>
              Храните полную историю взаимодействий с каждым клиентом. 
              Заметки, предпочтения, история посещений — всё в одном месте.
            </p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon">🔗</div>
          <div class="feature-content">
            <h3>Онлайн запись 24/7</h3>
            <p>
              Клиенты могут записываться самостоятельно через публичную страницу. 
              Вы получаете готовые записи без звонков и переписок.
            </p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon">📊</div>
          <div class="feature-content">
            <h3>Аналитика и отчёты</h3>
            <p>
              Отслеживайте доходы, загрузку, популярность услуг. 
              Принимайте решения на основе данных.
            </p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon">🔔</div>
          <div class="feature-content">
            <h3>Автоматические напоминания</h3>
            <p>
              SMS и email напоминания снижают количество неявок. 
              Клиенты не забудут о записи.
            </p>
          </div>
        </div>

        <div class="feature-item">
          <div class="feature-icon">💳</div>
          <div class="feature-content">
            <h3>Приём платежей</h3>
            <p>
              Интеграция с платёжными системами для приёма предоплат. 
              Автоматическое продление подписки.
            </p>
          </div>
        </div>
      </div>

      <div class="cta-section">
        <h2>Готовы попробовать?</h2>
        <router-link to="/register" class="btn btn-primary btn-lg">
          Начать бесплатно
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
// Features page component
</script>

<style scoped>
.features-page {
  padding: 80px 20px;
  min-height: 100vh;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
}

.page-title {
  text-align: center;
  font-size: 2.5rem;
  color: #1a202c;
  margin-bottom: 20px;
}

.page-subtitle {
  text-align: center;
  font-size: 1.25rem;
  color: #718096;
  margin-bottom: 60px;
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.feature-item {
  display: flex;
  gap: 30px;
  align-items: flex-start;
}

.feature-icon {
  font-size: 3rem;
  flex-shrink: 0;
}

.feature-content h3 {
  font-size: 1.5rem;
  color: #2d3748;
  margin-bottom: 10px;
}

.feature-content p {
  color: #4a5568;
  line-height: 1.8;
}

.cta-section {
  text-align: center;
  margin-top: 80px;
  padding: 60px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  color: white;
}

.cta-section h2 {
  font-size: 2rem;
  margin-bottom: 30px;
}

.btn {
  display: inline-block;
  padding: 15px 40px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
  border: none;
  cursor: pointer;
}

.btn-primary {
  background: white;
  color: #667eea;
}

.btn-primary:hover {
  background: #f7fafc;
}

.btn-lg {
  font-size: 1.125rem;
}

@media (max-width: 768px) {
  .feature-item {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .page-title {
    font-size: 2rem;
  }
}
</style>
