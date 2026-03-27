<template>
  <div class="profile-page">
    <h1 class="page-title">Профиль мастера</h1>

    <div v-if="loading" class="loading">
      <div class="spinner"></div>
    </div>

    <div v-else class="profile-form-container">
      <form @submit.prevent="saveProfile" class="profile-form">
        <div class="form-section">
          <h2>Основная информация</h2>
          <div class="form-group">
            <label>Отображаемое имя *</label>
            <input
              v-model="form.display_name"
              type="text"
              required
              class="input"
              placeholder="Как вас будут видеть клиенты"
            />
          </div>
          <div class="form-group">
            <label>О себе</label>
            <textarea
              v-model="form.bio"
              class="input"
              rows="4"
              placeholder="Расскажите о себе и своих услугах"
            ></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Телефон</label>
              <input
                v-model="form.phone"
                type="tel"
                class="input"
                placeholder="+7 (___) ___-__-__"
              />
            </div>
            <div class="form-group">
              <label>Email</label>
              <input
                v-model="form.email"
                type="email"
                class="input"
                placeholder="email@example.com"
              />
            </div>
          </div>
          <div class="form-group">
            <label>Адрес</label>
            <input
              v-model="form.address"
              type="text"
              class="input"
              placeholder="Город, улица, дом"
            />
          </div>
        </div>

        <div class="form-section">
          <h2>Настройки записи</h2>
          <div class="form-row">
            <div class="form-group">
              <label>Длительность записи (мин)</label>
              <input
                v-model.number="form.appointment_duration"
                type="number"
                min="15"
                max="180"
                step="15"
                class="input"
              />
            </div>
            <div class="form-group">
              <label>Дней вперёд для записи</label>
              <input
                v-model.number="form.booking_advance_days"
                type="number"
                min="1"
                max="365"
                class="input"
              />
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" :disabled="saving" class="btn btn-primary">
            {{ saving ? 'Сохранение...' : 'Сохранить' }}
          </button>
        </div>
      </form>

      <!-- Working Hours Section -->
      <div class="working-hours-section">
        <h2>Рабочие часы</h2>
        <div class="working-hours-form">
          <div
            v-for="(day, index) in workingHours"
            :key="index"
            class="working-day"
          >
            <div class="day-header">
              <span class="day-name">{{ day.name }}</span>
              <label class="toggle">
                <input
                  v-model="day.is_working_day"
                  type="checkbox"
                  @change="toggleDay(index)"
                />
                <span class="toggle-slider"></span>
              </label>
            </div>
            <div v-if="day.is_working_day" class="day-hours">
              <div class="time-inputs">
                <div class="time-group">
                  <label>Начало</label>
                  <input v-model="day.start_time" type="time" class="input-small" />
                </div>
                <div class="time-group">
                  <label>Конец</label>
                  <input v-model="day.end_time" type="time" class="input-small" />
                </div>
              </div>
              <div class="time-inputs">
                <div class="time-group">
                  <label>Перерыв с</label>
                  <input v-model="day.break_start" type="time" class="input-small" />
                </div>
                <div class="time-group">
                  <label>Перерыв до</label>
                  <input v-model="day.break_end" type="time" class="input-small" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <button @click="saveWorkingHours" class="btn btn-secondary">
          Сохранить график
        </button>
      </div>

      <!-- Services Section -->
      <div class="services-section">
        <div class="section-header">
          <h2>Услуги</h2>
          <button @click="showAddService = true" class="btn btn-primary btn-sm">
            + Добавить услугу
          </button>
        </div>
        <div v-if="services.length" class="services-list">
          <div v-for="service in services" :key="service.id" class="service-item">
            <div class="service-info">
              <h3>{{ service.name }}</h3>
              <p v-if="service.description" class="description">{{ service.description }}</p>
              <div class="service-meta">
                <span>{{ service.duration }} мин</span>
                <span>{{ formatPrice(service.price) }}</span>
              </div>
            </div>
            <div class="service-actions">
              <button @click="editService(service)" class="btn btn-sm btn-secondary">
                Редактировать
              </button>
              <button @click="deleteService(service.id)" class="btn btn-sm btn-danger">
                Удалить
              </button>
            </div>
          </div>
        </div>
        <p v-else class="no-services">Услуги ещё не добавлены</p>
      </div>
    </div>

    <!-- Add/Edit Service Modal -->
    <div v-if="showAddService || editingService" class="modal-overlay" @click="closeModal">
      <div class="modal" @click.stop>
        <h2>{{ editingService ? 'Редактировать услугу' : 'Новая услуга' }}</h2>
        <form @submit.prevent="saveService">
          <div class="form-group">
            <label>Название *</label>
            <input v-model="serviceForm.name" type="text" required class="input" />
          </div>
          <div class="form-group">
            <label>Описание</label>
            <textarea v-model="serviceForm.description" class="input" rows="3"></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Длительность (мин) *</label>
              <input v-model.number="serviceForm.duration" type="number" required min="15" class="input" />
            </div>
            <div class="form-group">
              <label>Цена (₽) *</label>
              <input v-model.number="serviceForm.price" type="number" required min="0" class="input" />
            </div>
          </div>
          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              Отмена
            </button>
            <button type="submit" :disabled="savingService" class="btn btn-primary">
              {{ savingService ? 'Сохранение...' : 'Сохранить' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { masterProfileApi } from '@/api/masterProfile';

const loading = ref(true);
const saving = ref(false);
const savingService = ref(false);
const showAddService = ref(false);
const editingService = ref(null);

const form = ref({
  display_name: '',
  bio: '',
  phone: '',
  email: '',
  address: '',
  appointment_duration: 60,
  booking_advance_days: 30,
});

const workingHours = ref([
  { day_of_week: 1, name: 'Понедельник', is_working_day: true, start_time: '09:00', end_time: '18:00', break_start: null, break_end: null },
  { day_of_week: 2, name: 'Вторник', is_working_day: true, start_time: '09:00', end_time: '18:00', break_start: null, break_end: null },
  { day_of_week: 3, name: 'Среда', is_working_day: true, start_time: '09:00', end_time: '18:00', break_start: null, break_end: null },
  { day_of_week: 4, name: 'Четверг', is_working_day: true, start_time: '09:00', end_time: '18:00', break_start: null, break_end: null },
  { day_of_week: 5, name: 'Пятница', is_working_day: true, start_time: '09:00', end_time: '18:00', break_start: null, break_end: null },
  { day_of_week: 6, name: 'Суббота', is_working_day: false, start_time: null, end_time: null, break_start: null, break_end: null },
  { day_of_week: 0, name: 'Воскресенье', is_working_day: false, start_time: null, end_time: null, break_start: null, break_end: null },
]);

const services = ref([]);

const serviceForm = reactive({
  name: '',
  description: '',
  duration: 60,
  price: 1000,
});

onMounted(async () => {
  await loadProfile();
  await loadWorkingHours();
  await loadServices();
});

async function loadProfile() {
  try {
    const data = await masterProfileApi.getProfile();
    if (data.profile) {
      form.value = { ...form.value, ...data.profile };
    }
  } catch (err) {
    console.error('Failed to load profile:', err);
  } finally {
    loading.value = false;
  }
}

async function saveProfile() {
  saving.value = true;
  try {
    if (form.value.id) {
      await masterProfileApi.updateProfile(form.value);
    } else {
      await masterProfileApi.createProfile(form.value);
    }
    alert('Профиль сохранён');
    await loadProfile();
  } catch (err) {
    alert('Ошибка при сохранении профиля');
    console.error(err);
  } finally {
    saving.value = false;
  }
}

async function loadWorkingHours() {
  try {
    const data = await masterProfileApi.getWorkingHours();
    if (data.working_hours?.length) {
      data.working_hours.forEach(wh => {
        const existing = workingHours.value.find(d => d.day_of_week === wh.day_of_week);
        if (existing) {
          existing.is_working_day = wh.is_working_day;
          existing.start_time = wh.start_time;
          existing.end_time = wh.end_time;
          existing.break_start = wh.break_start;
          existing.break_end = wh.break_end;
        }
      });
    }
  } catch (err) {
    console.error('Failed to load working hours:', err);
  }
}

async function saveWorkingHours() {
  try {
    await masterProfileApi.updateWorkingHours(workingHours.value);
    alert('График работы сохранён');
  } catch (err) {
    alert('Ошибка при сохранении графика');
    console.error(err);
  }
}

function toggleDay(index) {
  const day = workingHours.value[index];
  if (!day.is_working_day) {
    day.start_time = null;
    day.end_time = null;
    day.break_start = null;
    day.break_end = null;
  } else {
    day.start_time = '09:00';
    day.end_time = '18:00';
  }
}

async function loadServices() {
  try {
    const data = await masterProfileApi.getServices();
    services.value = data.services || [];
  } catch (err) {
    console.error('Failed to load services:', err);
  }
}

function editService(service) {
  editingService.value = service;
  serviceForm.name = service.name;
  serviceForm.description = service.description || '';
  serviceForm.duration = service.duration;
  serviceForm.price = service.price;
  showAddService.value = true;
}

async function saveService() {
  savingService.value = true;
  try {
    if (editingService.value) {
      await masterProfileApi.updateService(editingService.value.id, serviceForm);
    } else {
      await masterProfileApi.createService(serviceForm);
    }
    closeModal();
    await loadServices();
  } catch (err) {
    alert('Ошибка при сохранении услуги');
    console.error(err);
  } finally {
    savingService.value = false;
  }
}

async function deleteService(id) {
  if (!confirm('Вы уверены, что хотите удалить услугу?')) return;
  try {
    await masterProfileApi.deleteService(id);
    await loadServices();
  } catch (err) {
    alert('Ошибка при удалении услуги');
    console.error(err);
  }
}

function closeModal() {
  showAddService.value = false;
  editingService.value = null;
  serviceForm.name = '';
  serviceForm.description = '';
  serviceForm.duration = 60;
  serviceForm.price = 1000;
}

function formatPrice(price) {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
  }).format(price);
}
</script>

<style scoped>
.profile-page {
  padding: 40px;
  max-width: 1000px;
  margin: 0 auto;
}

.page-title {
  font-size: 2rem;
  color: #1a202c;
  margin-bottom: 30px;
}

.loading {
  text-align: center;
  padding: 60px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.profile-form-container {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.form-section {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.form-section h2 {
  font-size: 1.25rem;
  color: #2d3748;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: #4a5568;
  margin-bottom: 8px;
}

.input {
  width: 100%;
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

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
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

.btn-danger {
  background: #fc8181;
  color: white;
}

.btn-danger:hover {
  background: #f56565;
}

.btn-sm {
  padding: 8px 20px;
  font-size: 0.875rem;
}

.working-hours-section,
.services-section {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.working-hours-section h2,
.services-section h2 {
  font-size: 1.25rem;
  color: #2d3748;
  margin-bottom: 20px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.working-day {
  padding: 15px 0;
  border-bottom: 1px solid #e2e8f0;
}

.day-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.day-name {
  font-weight: 500;
  color: #2d3748;
}

.toggle {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
}

.toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #cbd5e0;
  transition: 0.3s;
  border-radius: 26px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.3s;
  border-radius: 50%;
}

.toggle input:checked + .toggle-slider {
  background-color: #667eea;
}

.toggle input:checked + .toggle-slider:before {
  transform: translateX(24px);
}

.day-hours {
  margin-top: 15px;
  padding: 15px;
  background: #f7fafc;
  border-radius: 8px;
}

.time-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
  margin-bottom: 10px;
}

.time-group label {
  display: block;
  font-size: 0.875rem;
  color: #718096;
  margin-bottom: 5px;
}

.input-small {
  padding: 8px 12px;
  border: 2px solid #e2e8f0;
  border-radius: 6px;
  font-size: 0.875rem;
  width: 100%;
}

.services-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.service-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
}

.service-info h3 {
  font-size: 1rem;
  color: #2d3748;
  margin-bottom: 5px;
}

.description {
  font-size: 0.875rem;
  color: #718096;
  margin-bottom: 10px;
}

.service-meta {
  display: flex;
  gap: 15px;
  font-size: 0.875rem;
  color: #4a5568;
}

.service-actions {
  display: flex;
  gap: 10px;
}

.no-services {
  text-align: center;
  color: #718096;
  padding: 40px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 40px;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal h2 {
  font-size: 1.25rem;
  color: #2d3748;
  margin-bottom: 20px;
}

.modal-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  margin-top: 20px;
}

@media (max-width: 768px) {
  .profile-page {
    padding: 20px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .service-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }

  .time-inputs {
    grid-template-columns: 1fr;
  }
}
</style>
