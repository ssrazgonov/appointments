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
        <!-- Step 1: Select Service -->
        <section class="form-section" :class="{ active: step === 1 }">
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
        <section v-if="step >= 2" class="form-section" :class="{ active: step === 2 }">
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
        <section v-if="step >= 3" class="form-section" :class="{ active: step === 3 }">
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
              @click="step = 4"
              class="btn btn-primary"
            >
              Продолжить
            </button>
          </div>
        </section>

        <!-- Step 4: Client Info -->
        <section v-if="step >= 4" class="form-section" :class="{ active: step === 4 }">
          <h2>Ваши данные</h2>
          <form @submit.prevent="submitBooking" class="client-form">
            <div class="form-group">
              <label>Имя *</label>
              <input
                v-model="formData.client_name"
                type="text"
                required
                class="input"
                placeholder="Ваше имя"
              />
            </div>
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
            <div class="form-group">
              <label>Email</label>
              <input
                v-model="formData.client_email"
                type="email"
                class="input"
                placeholder="email@example.com"
              />
            </div>
            <div class="form-group">
              <label>Комментарий</label>
              <textarea
                v-model="formData.notes"
                class="input"
                rows="3"
                placeholder="Пожелания к записи"
              ></textarea>
            </div>

            <!-- Booking Summary -->
            <div class="booking-summary">
              <h3>Детали записи</h3>
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

            <div class="form-actions">
              <button type="button" @click="step = 3" class="btn btn-secondary">Назад</button>
              <button type="submit" :disabled="submitting" class="btn btn-primary">
                {{ submitting ? 'Запись...' : 'Записаться' }}
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

const route = useRoute();
const router = useRouter();

const master = ref(null);
const services = ref([]);
const loading = ref(true);
const error = ref(null);
const loadingSlots = ref(false);
const submitting = ref(false);

const step = ref(1);
const selectedService = ref(null);
const selectedDate = ref(null);
const selectedSlot = ref(null);
const timeSlots = ref([]);

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
    dates.push({
      date: date.toISOString().split('T')[0],
      dayName: dayNames[date.getDay()],
      dayNumber: date.getDate(),
      monthName: monthNames[date.getMonth()],
    });
  }

  return dates;
});

onMounted(() => {
  loadMaster();
});

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

async function submitBooking() {
  submitting.value = true;

  try {
    const startTime = new Date(`${selectedDate.value}T${selectedSlot.value.start}`);
    const endTime = new Date(`${selectedDate.value}T${selectedSlot.value.end}`);

    await bookingApi.createBooking({
      user_id: master.value.user_id,
      service_id: selectedService.value?.id,
      start_time: startTime.toISOString(),
      end_time: endTime.toISOString(),
      client_name: formData.value.client_name,
      client_phone: formData.value.client_phone,
      client_email: formData.value.client_email,
      notes: formData.value.notes,
    });

    router.push(`/book/${route.params.slug}/success`);
  } catch (err) {
    alert('Ошибка при создании записи. Попробуйте снова.');
    console.error(err);
  } finally {
    submitting.value = false;
  }
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
  padding: 40px 20px;
}

.booking-container {
  max-width: 600px;
  margin: 0 auto;
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

.form-section {
  display: none;
}

.form-section.active {
  display: block;
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

.client-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
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
  padding: 60px 20px;
  color: #718096;
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
}
</style>
