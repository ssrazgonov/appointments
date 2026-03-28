<template>
  <div class="booking-page">
    <div class="booking-container">
      <!-- Master Info Header -->
      <header v-if="master" class="master-header">
        <div class="master-avatar">
          <img v-if="master.avatar_url" :src="master.avatar_url" :alt="master.display_name" />
          <div v-else class="avatar-placeholder">{{ master.display_name[0] }}</div>
        </div>
        <div class="master-info">
          <h1>{{ master.display_name }}</h1>
          <p v-if="master.bio" class="bio">{{ master.bio }}</p>
        </div>
      </header>

      <!-- Loading State -->
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Загрузка...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error">
        <p>{{ error }}</p>
        <button @click="loadMaster" class="btn btn-primary">Попробовать снова</button>
      </div>

      <!-- Booking Form -->
      <div v-else-if="master && services.length" class="booking-form">
        <!-- Welcome Message for Logged In Users -->
        <div v-if="isLoggedIn" class="welcome-banner">
          <span class="welcome-icon">✓</span>
          <div class="welcome-text">
            <strong>Вы вошли как {{ clientName }}</strong>
            <p>Запись будет создана на ваш аккаунт</p>
          </div>
          <button @click="handleLogout" class="btn btn-sm btn-outline">Выйти</button>
        </div>

        <!-- Step 1: Select Service -->
        <section v-show="step >= 1" class="form-section" :class="{ active: step === 1 }">
          <h2>Выберите услугу</h2>
          <div class="services-list">
            <div
              v-for="service in services"
              :key="service.id"
              class="service-card"
              :class="{ selected: selectedService?.id === service.id }"
              @click="selectService(service)"
            >
              <div class="service-info">
                <h3>{{ service.name }}</h3>
                <p v-if="service.description" class="description">{{ service.description }}</p>
              </div>
              <div class="service-meta">
                <span class="duration">{{ service.duration }} мин</span>
                <span class="price">{{ formatPrice(service.price) }}</span>
              </div>
            </div>
          </div>
          <button
            v-if="selectedService"
            @click="step = 2"
            class="btn btn-primary btn-block"
          >
            Продолжить
          </button>
        </section>

        <!-- Step 2: Select Date -->
        <section v-show="step >= 2" class="form-section" :class="{ active: step === 2 }">
          <h2>Выберите дату</h2>
          <div class="date-picker">
            <div
              v-for="date in availableDates"
              :key="date.date"
              class="date-card"
              :class="{ selected: selectedDate === date.date }"
              @click="selectDate(date.date)"
            >
              <div class="date-day">{{ date.dayName }}</div>
              <div class="date-number">{{ date.dayNumber }}</div>
              <div class="date-month">{{ date.monthName }}</div>
            </div>
          </div>
          <div class="form-actions">
            <button @click="step = 1" class="btn btn-secondary">Назад</button>
            <button
              v-if="selectedDate"
              @click="loadTimeSlots"
              class="btn btn-primary"
            >
              Продолжить
            </button>
          </div>
        </section>

        <!-- Step 3: Select Time -->
        <section v-show="step >= 3" class="form-section" :class="{ active: step === 3 }">
          <h2>Выберите время</h2>
          <div v-if="loadingSlots" class="loading">
            <div class="spinner"></div>
          </div>
          <div v-else-if="timeSlots.length" class="time-slots">
            <button
              v-for="slot in timeSlots"
              :key="slot.start"
              class="time-slot"
              :class="{ selected: selectedSlot === slot }"
              @click="selectSlot(slot)"
            >
              {{ slot.start }}
            </button>
          </div>
          <p v-else class="no-slots">Нет доступных времён на эту дату</p>
          <div class="form-actions">
            <button @click="step = 2" class="btn btn-secondary">Назад</button>
            <button
              v-if="selectedSlot"
              @click="isLoggedIn ? submitBooking() : (step = 4)"
              class="btn btn-primary"
            >
              {{ isLoggedIn ? 'Записаться' : 'Продолжить' }}
            </button>
          </div>
        </section>

        <!-- Step 4: Phone Input (for new users) -->
        <section v-show="step >= 4 && !isLoggedIn" class="form-section" :class="{ active: step === 4 }">
          <h2>Ваш телефон</h2>
          <p class="section-description">
            Мы создадим аккаунт автоматически. На телефон придёт код подтверждения.
          </p>
          <form @submit.prevent="sendVerificationCode" class="phone-form">
            <div class="form-group">
              <label>Телефон *</label>
              <input
                v-model="formData.client_phone"
                type="tel"
                required
                class="input"
                placeholder="+7 (___) ___-__-__"
              />
            </div>
            <div class="form-actions">
              <button type="button" @click="step = 3" class="btn btn-secondary">Назад</button>
              <button type="submit" :disabled="sendingCode" class="btn btn-primary">
                {{ sendingCode ? 'Отправка...' : 'Получить код' }}
              </button>
            </div>
          </form>
        </section>

        <!-- Step 5: Verification Code (for new users) -->
        <section v-show="step >= 5 && !isLoggedIn" class="form-section" :class="{ active: step === 5 }">
          <h2>Код подтверждения</h2>
          <p class="section-description">
            Введите код из SMS сообщения
          </p>
          <form @submit.prevent="verifyCode" class="code-form">
            <div class="form-group">
              <label>Код *</label>
              <input
                v-model="verificationCode"
                type="text"
                maxlength="6"
                required
                class="input"
                placeholder="000000"
              />
            </div>
            <div v-if="codeError" class="error-message">{{ codeError }}</div>
            <div class="form-actions">
              <button type="button" @click="step = 4" class="btn btn-secondary">Назад</button>
              <button type="submit" :disabled="verifying" class="btn btn-primary">
                {{ verifying ? 'Проверка...' : 'Подтвердить' }}
              </button>
            </div>
          </form>
        </section>

        <!-- Step 6: Final Confirmation -->
        <section v-show="step >= 6" class="form-section" :class="{ active: step === 6 }">
          <h2>Подтверждение записи</h2>
          <form @submit.prevent="submitBooking" class="confirmation-form">
            <!-- Booking Summary -->
            <div class="booking-summary">
              <h3>Детали записи</h3>
              <div class="summary-row">
                <span>Мастер:</span>
                <strong>{{ master.display_name }}</strong>
              </div>
              <div class="summary-row">
                <span>Услуга:</span>
                <strong>{{ selectedService?.name }}</strong>
              </div>
              <div class="summary-row">
                <span>Дата:</span>
                <strong>{{ formatDate(selectedDate) }}</strong>
              </div>
              <div class="summary-row">
                <span>Время:</span>
                <strong>{{ selectedSlot?.start }} - {{ selectedSlot?.end }}</strong>
              </div>
              <div class="summary-row total">
                <span>Итого:</span>
                <strong>{{ formatPrice(selectedService?.price || 0) }}</strong>
              </div>
            </div>

            <div class="form-group" v-if="!isLoggedIn">
              <label>Ваше имя</label>
              <input
                v-model="formData.client_name"
                type="text"
                class="input"
                placeholder="Как к вам обращаться"
              />
            </div>

            <div class="form-actions">
              <button type="button" @click="step = isLoggedIn ? 3 : 5" class="btn btn-secondary">Назад</button>
              <button type="submit" :disabled="submitting" class="btn btn-primary">
                {{ submitting ? 'Запись...' : 'Подтвердить запись' }}
              </button>
            </div>
          </form>
        </section>
      </div>

      <div v-else-if="master" class="no-services">
        <p>Услуги временно недоступны</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { bookingApi } from '@/api/booking';
import { clientAuthApi } from '@/api/clientAuth';

const route = useRoute();
const router = useRouter();

const master = ref(null);
const services = ref([]);
const loading = ref(true);
const error = ref(null);
const loadingSlots = ref(false);
const submitting = ref(false);
const sendingCode = ref(false);
const verifying = ref(false);

const step = ref(1);
const selectedService = ref(null);
const selectedDate = ref(null);
const selectedSlot = ref(null);
const timeSlots = ref([]);
const verificationCode = ref('');
const codeError = ref('');
const clientToken = ref(null);

// Auth state
const isLoggedIn = ref(false);
const clientName = ref('');

const formData = ref({
  client_name: '',
  client_phone: '',
  client_email: '',
  notes: '',
});

const availableDates = computed(() => {
  const dates = [];
  const today = new Date();
  const dayNames = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
  const monthNames = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];

  for (let i = 0; i < 14; i++) {
    const date = new Date(today);
    date.setDate(date.getDate() + i);
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const dateString = `${year}-${month}-${day}`;
    
    dates.push({
      date: dateString,
      dayName: dayNames[date.getDay()],
      dayNumber: date.getDate(),
      monthName: monthNames[date.getMonth()],
    });
  }

  return dates;
});

onMounted(() => {
  checkAuth();
  loadMaster();
});

function checkAuth() {
  const token = clientAuthApi.getToken();
  const user = clientAuthApi.getUser();
  
  if (token && user) {
    isLoggedIn.value = true;
    clientName.value = user.name || user.phone;
    formData.value.client_phone = user.phone;
    formData.value.client_name = user.name || '';
    formData.value.client_email = user.email || '';
  }
}

async function loadMaster() {
  loading.value = true;
  error.value = null;

  try {
    const data = await bookingApi.getMasterBySlug(route.params.slug);
    master.value = data.master;
    const servicesData = await bookingApi.getServices(master.value.user_id);
    services.value = servicesData.services;
  } catch (err) {
    error.value = 'Не удалось загрузить данные мастера';
    console.error(err);
  } finally {
    loading.value = false;
  }
}

function selectService(service) {
  selectedService.value = service;
}

function selectDate(date) {
  selectedDate.value = date;
}

async function loadTimeSlots() {
  loadingSlots.value = true;
  timeSlots.value = [];
  selectedSlot.value = null;

  try {
    const data = await bookingApi.getAvailableSlots(
      master.value.user_id,
      selectedDate.value,
      selectedService.value?.id
    );
    timeSlots.value = data.slots || [];
    step.value = 3;
  } catch (err) {
    console.error(err);
  } finally {
    loadingSlots.value = false;
  }
}

function selectSlot(slot) {
  selectedSlot.value = slot;
}

async function sendVerificationCode() {
  if (!formData.value.client_phone) {
    codeError.value = 'Введите номер телефона';
    return;
  }

  sendingCode.value = true;
  codeError.value = '';

  try {
    const response = await clientAuthApi.sendVerificationCode(formData.value.client_phone);
    
    // For testing - show the code
    if (response.code) {
      alert(`Код подтверждения: ${response.code}`);
    }
    
    step.value = 5;
  } catch (err) {
    codeError.value = err.response?.data?.message || 'Ошибка отправки кода';
  } finally {
    sendingCode.value = false;
  }
}

async function verifyCode() {
  if (!verificationCode.value) {
    codeError.value = 'Введите код';
    return;
  }

  verifying.value = true;
  codeError.value = '';

  try {
    const response = await clientAuthApi.verifyCode(formData.value.client_phone, verificationCode.value);
    
    // Login and get token
    clientToken.value = response.token;
    isLoggedIn.value = true;
    clientName.value = response.client?.name || response.client?.phone;
    
    if (response.client) {
      formData.value.client_name = response.client.name || '';
      formData.value.client_email = response.client.email || '';
    }
    
    step.value = 6;
  } catch (err) {
    codeError.value = err.response?.data?.message || 'Неверный код';
  } finally {
    verifying.value = false;
  }
}

async function submitBooking() {
  submitting.value = true;

  try {
    const startTime = new Date(`${selectedDate.value}T${selectedSlot.value.start}`);
    const endTime = new Date(`${selectedDate.value}T${selectedSlot.value.end}`);

    const bookingData = {
      user_id: master.value.user_id,
      service_id: selectedService.value?.id,
      start_time: startTime.toISOString(),
      end_time: endTime.toISOString(),
      client_name: formData.value.client_name || 'Клиент',
      client_phone: formData.value.client_phone,
      client_email: formData.value.client_email,
      notes: formData.value.notes,
    };

    // If logged in, use token
    if (isLoggedIn.value && clientToken.value) {
      bookingData.client_token = clientToken.value;
    }

    const response = await bookingApi.createBooking(bookingData);

    // Redirect to success page or client cabinet
    if (isLoggedIn.value) {
      // Force reload to load client cabinet app
      window.location.href = '/client/appointments?booking=success';
    } else {
      // Auto-login and redirect - save token AND user info
      if (response.client_token) {
        localStorage.setItem('client_token', response.client_token);
        // Save user info from response or from form
        const userInfo = response.appointment ? {
          name: response.appointment.client_name,
          phone: response.appointment.client_phone,
          email: response.appointment.client_email,
        } : {
          name: formData.value.client_name,
          phone: formData.value.client_phone,
          email: formData.value.client_email,
        };
        localStorage.setItem('client_user', JSON.stringify(userInfo));
      }
      // Force reload to load client cabinet app
      window.location.href = '/client/appointments?booking=success';
    }
  } catch (err) {
    alert('Ошибка при создании записи. Попробуйте снова.');
    console.error(err);
  } finally {
    submitting.value = false;
  }
}

async function handleLogout() {
  // Clear local storage
  localStorage.removeItem('client_token');
  localStorage.removeItem('client_user');
  
  // Call API logout
  try {
    await clientAuthApi.logout();
  } catch (err) {
    console.error('Logout error:', err);
  }
  
  // Reset state
  isLoggedIn.value = false;
  clientName.value = '';
  clientToken.value = null;
  formData.value.client_name = '';
  formData.value.client_phone = '';
  formData.value.client_email = '';
  
  // Reload page
  window.location.reload();
}

function formatPrice(price) {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
  }).format(price);
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('ru-RU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
}
</script>

<style scoped>
.booking-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.booking-container {
  max-width: 600px;
  width: 100%;
  background: white;
  border-radius: 16px;
  padding: 40px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.master-header {
  display: flex;
  align-items: center;
  gap: 20px;
  padding-bottom: 30px;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 30px;
}

.master-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.master-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
}

.master-info h1 {
  font-size: 1.5rem;
  color: #1a202c;
  margin-bottom: 5px;
}

.bio {
  color: #718096;
  font-size: 0.875rem;
}

.loading {
  text-align: center;
  padding: 60px 20px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error {
  text-align: center;
  padding: 60px 20px;
  color: #e53e3e;
}

.booking-form {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.form-section {
  display: block;
}

.form-section:not(.active) {
  display: none;
}

.form-section h2 {
  font-size: 1.25rem;
  color: #1a202c;
  margin-bottom: 20px;
}

.services-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 20px;
}

.service-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s;
}

.service-card:hover {
  border-color: #667eea;
}

.service-card.selected {
  border-color: #667eea;
  background: #f7fafc;
}

.service-info h3 {
  font-size: 1rem;
  color: #2d3748;
  margin-bottom: 5px;
}

.description {
  font-size: 0.875rem;
  color: #718096;
}

.service-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 5px;
}

.duration {
  font-size: 0.875rem;
  color: #718096;
}

.price {
  font-weight: bold;
  color: #2d3748;
}

.date-picker {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 10px;
  margin-bottom: 20px;
}

.date-card {
  padding: 15px 10px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
}

.date-card:hover {
  border-color: #667eea;
}

.date-card.selected {
  border-color: #667eea;
  background: #667eea;
  color: white;
}

.date-day {
  font-size: 0.75rem;
  margin-bottom: 5px;
}

.date-number {
  font-size: 1.25rem;
  font-weight: bold;
}

.date-month {
  font-size: 0.75rem;
}

.time-slots {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-bottom: 20px;
}

.time-slot {
  padding: 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  transition: all 0.3s;
  font-weight: 500;
}

.time-slot:hover {
  border-color: #667eea;
}

.time-slot.selected {
  border-color: #667eea;
  background: #667eea;
  color: white;
}

.no-slots {
  text-align: center;
  color: #718096;
  padding: 40px 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 20px;
}

.form-group label {
  font-weight: 500;
  color: #2d3748;
}

.input {
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.input:focus {
  outline: none;
  border-color: #667eea;
}

.booking-summary {
  background: #f7fafc;
  padding: 20px;
  border-radius: 12px;
  margin-top: 20px;
}

.booking-summary h3 {
  font-size: 1rem;
  color: #2d3748;
  margin-bottom: 15px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #e2e8f0;
}

.summary-row:last-child {
  border-bottom: none;
}

.summary-row.total {
  font-size: 1.125rem;
  margin-top: 10px;
  padding-top: 15px;
  border-top: 2px solid #e2e8f0;
}

.form-actions {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.btn {
  padding: 12px 30px;
  border-radius: 8px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5568d3;
}

.btn-primary:disabled {
  background: #a0aec0;
  cursor: not-allowed;
}

.btn-secondary {
  background: #e2e8f0;
  color: #2d3748;
}

.btn-secondary:hover {
  background: #cbd5e0;
}

.btn-block {
  width: 100%;
}

.no-services {
  text-align: center;
  color: #718096;
  padding: 60px 20px;
}

/* New styles for auth flow */
.welcome-banner {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 20px;
  background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%);
  border-radius: 12px;
  margin-bottom: 30px;
}

.welcome-icon {
  font-size: 2rem;
}

.welcome-text {
  flex: 1;
}

.welcome-text strong {
  display: block;
  color: #22543d;
  font-size: 1.125rem;
  margin-bottom: 5px;
}

.welcome-text p {
  color: #2f855a;
  font-size: 0.875rem;
  margin: 0;
}

.section-description {
  color: #718096;
  font-size: 0.875rem;
  margin-bottom: 20px;
  line-height: 1.6;
}

.phone-form,
.code-form,
.confirmation-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.error-message {
  color: #e53e3e;
  background: #fff5f5;
  padding: 12px 16px;
  border-radius: 8px;
  font-size: 0.875rem;
  border-left: 4px solid #e53e3e;
}

.btn-sm {
  padding: 8px 16px;
  font-size: 0.875rem;
}

.btn-outline {
  background: transparent;
  color: #2f855a;
  border: 2px solid #2f855a;
}

.btn-outline:hover {
  background: #2f855a;
  color: white;
}

@media (max-width: 640px) {
  .booking-container {
    padding: 20px;
  }

  .date-picker {
    grid-template-columns: repeat(4, 1fr);
  }

  .time-slots {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .master-header {
    flex-direction: column;
    text-align: center;
  }
}
</style>
