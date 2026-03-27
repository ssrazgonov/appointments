<template>
  <div class="pricing-page">
    <div class="container">
      <h1 class="page-title">Простые и прозрачные тарифы</h1>
      <p class="page-subtitle">
        Выберите план, который подходит вашему бизнесу
      </p>

      <div class="pricing-grid">
        <div class="pricing-card">
          <h3>Starter</h3>
          <div class="price">990 ₽<span>/мес</span></div>
          <p class="plan-description">Для начинающих мастеров</p>
          <ul class="pricing-features">
            <li>✓ До 50 клиентов</li>
            <li>✓ До 10 записей в день</li>
            <li>✓ Базовые отчёты</li>
            <li>✓ Email поддержка</li>
            <li>✓ 7 дней бесплатно</li>
          </ul>
          <router-link to="/register" class="btn btn-outline">
            Начать бесплатно
          </router-link>
        </div>

        <div class="pricing-card featured">
          <div class="badge">Популярный выбор</div>
          <h3>Professional</h3>
          <div class="price">1 990 ₽<span>/мес</span></div>
          <p class="plan-description">Для растущего бизнеса</p>
          <ul class="pricing-features">
            <li>✓ До 200 клиентов</li>
            <li>✓ До 30 записей в день</li>
            <li>✓ Расширенные отчёты</li>
            <li>✓ SMS напоминания</li>
            <li>✓ Приоритетная поддержка</li>
            <li>✓ 7 дней бесплатно</li>
          </ul>
          <router-link to="/register" class="btn btn-primary">
            Начать бесплатно
          </router-link>
        </div>

        <div class="pricing-card">
          <h3>Business</h3>
          <div class="price">3 990 ₽<span>/мес</span></div>
          <p class="plan-description">Для максимальных возможностей</p>
          <ul class="pricing-features">
            <li>✓ Безлимитные клиенты</li>
            <li>✓ До 100 записей в день</li>
            <li>✓ Полная аналитика</li>
            <li>✓ SMS & Email напоминания</li>
            <li>✓ API доступ</li>
            <li>✓ 24/7 поддержка</li>
            <li>✓ 7 дней бесплатно</li>
          </ul>
          <router-link to="/register" class="btn btn-outline">
            Начать бесплатно
          </router-link>
        </div>
      </div>

      <div class="faq-section">
        <h2>Частые вопросы</h2>
        
        <div class="faq-item">
          <h3>Могу ли я изменить тариф позже?</h3>
          <p>Да, вы можете перейти на другой тариф в любое время из личного кабинета.</p>
        </div>

        <div class="faq-item">
          <h3>Что происходит после пробного периода?</h3>
          <p>После 7 дней бесплатного использования вам нужно будет выбрать тариф для продолжения работы.</p>
        </div>

        <div class="faq-item">
          <h3>Как происходит оплата?</h3>
          <p>Оплата производится банковской картой через защищённый платёжный шлюз Robokassa.</p>
        </div>

        <div class="faq-item">
          <h3>Можно ли отменить подписку?</h3>
          <p>Да, вы можете отменить подписку в любой момент. Доступ сохранится до конца оплаченного периода.</p>
        </div>
      </div>

      <div class="cta-section">
        <h2>Остались вопросы?</h2>
        <p>Напишите нам, и мы поможем выбрать подходящий тариф</p>
        <a href="mailto:support@recordtomaster.ru" class="btn btn-secondary">
          Связаться с поддержкой
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
// Pricing page component
</script>

<style scoped>
.pricing-page {
  padding: 80px 20px;
  min-height: 100vh;
}

.container {
  max-width: 1200px;
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

.pricing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
  margin-bottom: 100px;
}

.pricing-card {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
  border: 2px solid transparent;
  position: relative;
}

.pricing-card.featured {
  border-color: #667eea;
  transform: scale(1.05);
}

.pricing-card .badge {
  position: absolute;
  top: -15px;
  left: 50%;
  transform: translateX(-50%);
  background: #667eea;
  color: white;
  padding: 5px 20px;
  border-radius: 20px;
  font-size: 0.875rem;
}

.price {
  font-size: 2.5rem;
  font-weight: bold;
  color: #2d3748;
  margin: 20px 0;
}

.price span {
  font-size: 1rem;
  color: #718096;
}

.plan-description {
  color: #718096;
  margin-bottom: 30px;
}

.pricing-features {
  list-style: none;
  padding: 0;
  margin: 30px 0;
  text-align: left;
}

.pricing-features li {
  padding: 12px 0;
  color: #4a5568;
  border-bottom: 1px solid #e2e8f0;
}

.faq-section {
  margin-top: 80px;
}

.faq-section h2 {
  text-align: center;
  font-size: 2rem;
  color: #1a202c;
  margin-bottom: 40px;
}

.faq-item {
  background: white;
  padding: 30px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.faq-item h3 {
  font-size: 1.25rem;
  color: #2d3748;
  margin-bottom: 15px;
}

.faq-item p {
  color: #4a5568;
  line-height: 1.8;
}

.cta-section {
  text-align: center;
  margin-top: 80px;
  padding: 60px 20px;
  background: #f7fafc;
  border-radius: 16px;
}

.cta-section h2 {
  font-size: 2rem;
  color: #1a202c;
  margin-bottom: 15px;
}

.cta-section p {
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
  border: none;
  cursor: pointer;
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

.btn-secondary {
  background: #e2e8f0;
  color: #2d3748;
}

.btn-secondary:hover {
  background: #cbd5e0;
}

@media (max-width: 768px) {
  .pricing-card.featured {
    transform: none;
  }
  
  .page-title {
    font-size: 2rem;
  }
}
</style>
