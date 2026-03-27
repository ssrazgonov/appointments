<template>
  <div class="landing-page">
    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <h1 class="hero-title">RecordToMaster</h1>
        <p class="hero-subtitle">
          CRM система для мастеров красоты, ремонтных специалистов и частных мастеров
        </p>
        <div class="hero-actions">
          <router-link to="/register" class="btn btn-primary btn-lg">
            Начать бесплатно
          </router-link>
          <a href="#features" class="btn btn-secondary btn-lg">
            Узнать больше
          </a>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
      <div class="container">
        <h2 class="section-title">Возможности</h2>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">📅</div>
            <h3>Управление записями</h3>
            <p>Календарь записей, напоминания и автоматическое подтверждение</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">👥</div>
            <h3>База клиентов</h3>
            <p>История посещений, контакты и заметки по каждому клиенту</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Отчёты и аналитика</h3>
            <p>Доходы, статистика записей и анализ бизнеса</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">🔗</div>
            <h3>Онлайн запись</h3>
            <p>Публичная страница для записи клиентов 24/7</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">💳</div>
            <h3>Приём платежей</h3>
            <p>Интеграция с платёжными системами</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h3>Мобильность</h3>
            <p>Работает на любом устройстве через браузер</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
      <div class="container">
        <h2 class="section-title">Тарифы</h2>
        <div class="pricing-grid">
          <div class="pricing-card">
            <h3>Starter</h3>
            <div class="price">990 ₽<span>/мес</span></div>
            <ul class="pricing-features">
              <li>✓ До 50 клиентов</li>
              <li>✓ До 10 записей в день</li>
              <li>✓ Базовые отчёты</li>
              <li>✓ Email поддержка</li>
            </ul>
            <router-link to="/register" class="btn btn-primary">
              Выбрать
            </router-link>
          </div>
          <div class="pricing-card featured">
            <div class="badge">Популярный</div>
            <h3>Professional</h3>
            <div class="price">1 990 ₽<span>/мес</span></div>
            <ul class="pricing-features">
              <li>✓ До 200 клиентов</li>
              <li>✓ До 30 записей в день</li>
              <li>✓ Расширенные отчёты</li>
              <li>✓ SMS напоминания</li>
              <li>✓ Приоритетная поддержка</li>
            </ul>
            <router-link to="/register" class="btn btn-primary">
              Выбрать
            </router-link>
          </div>
          <div class="pricing-card">
            <h3>Business</h3>
            <div class="price">3 990 ₽<span>/мес</span></div>
            <ul class="pricing-features">
              <li>✓ Безлимитные клиенты</li>
              <li>✓ До 100 записей в день</li>
              <li>✓ Полная аналитика</li>
              <li>✓ SMS & Email напоминания</li>
              <li>✓ API доступ</li>
              <li>✓ 24/7 поддержка</li>
            </ul>
            <router-link to="/register" class="btn btn-primary">
              Выбрать
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
      <div class="container">
        <h2>Готовы начать?</h2>
        <p>7 дней бесплатного пробного периода. Карта не требуется.</p>
        <router-link to="/register" class="btn btn-primary btn-lg">
          Зарегистрироваться бесплатно
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
// Home page component
</script>

<style scoped>
.landing-page {
  font-family: 'Inter', system-ui, sans-serif;
}

.hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 100px 20px;
  text-align: center;
}

.hero-title {
  font-size: 3rem;
  margin-bottom: 20px;
}

.hero-subtitle {
  font-size: 1.25rem;
  margin-bottom: 40px;
  opacity: 0.9;
}

.hero-actions {
  display: flex;
  gap: 20px;
  justify-content: center;
  flex-wrap: wrap;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 60px;
  color: #1a202c;
}

.features {
  padding: 100px 20px;
  background: #f7fafc;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
}

.feature-card {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 20px;
}

.feature-card h3 {
  margin-bottom: 15px;
  color: #2d3748;
}

.feature-card p {
  color: #718096;
  line-height: 1.6;
}

.pricing {
  padding: 100px 20px;
}

.pricing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
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

.pricing-features {
  list-style: none;
  padding: 0;
  margin: 30px 0;
  text-align: left;
}

.pricing-features li {
  padding: 10px 0;
  color: #4a5568;
  border-bottom: 1px solid #e2e8f0;
}

.cta {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 80px 20px;
  text-align: center;
}

.cta h2 {
  font-size: 2.5rem;
  margin-bottom: 20px;
}

.cta p {
  font-size: 1.25rem;
  margin-bottom: 40px;
  opacity: 0.9;
}

.footer {
  background: #1a202c;
  color: white;
  padding: 40px 20px;
  text-align: center;
}

.btn {
  display: inline-block;
  padding: 12px 30px;
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

.btn-secondary {
  background: white;
  color: #667eea;
}

.btn-secondary:hover {
  background: #f7fafc;
}

.btn-lg {
  padding: 15px 40px;
  font-size: 1.125rem;
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .pricing-card.featured {
    transform: none;
  }
}
</style>
