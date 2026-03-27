<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>

    <!-- Booking Link Widget -->
    <div v-if="masterProfile?.slug" class="card booking-link-card">
      <div class="booking-link-content">
        <div class="booking-link-info">
          <h3 class="booking-link-title">📱 Ваша ссылка для записи</h3>
          <div class="booking-link-row">
            <input
              type="text"
              readonly
              :value="bookingUrl"
              class="input booking-url-input"
            />
            <button @click="copyLink" class="btn btn-secondary">
              {{ linkCopied ? '✓ Скопировано' : '📋 Копировать' }}
            </button>
            <a :href="bookingUrl" target="_blank" class="btn btn-primary">
              🔗 Открыть
            </a>
          </div>
          <p class="booking-link-hint">
            Отправьте эту ссылку клиентам или разместите в соцсетях
          </p>
        </div>
        <div class="booking-qr-code">
          <qrcode-vue
            :value="bookingUrl"
            :size="120"
            level="H"
            render-as="svg"
          />
          <button @click="downloadQR" class="btn btn-sm btn-outline">
            📥 Скачать QR
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="card">
        <div class="flex items-center">
          <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Записей сегодня</dt>
              <dd class="text-2xl font-semibold text-gray-900">
                {{ dashboard.today?.appointments_count || 0 }}
              </dd>
            </dl>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center">
          <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Доход за месяц</dt>
              <dd class="text-2xl font-semibold text-gray-900">
                {{ formatCurrency(dashboard.month?.revenue || 0) }}
              </dd>
            </dl>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center">
          <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Всего клиентов</dt>
              <dd class="text-2xl font-semibold text-gray-900">
                {{ dashboard.clients?.total || 0 }}
              </dd>
            </dl>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex items-center">
          <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate">Подписка</dt>
              <dd class="text-sm font-semibold text-gray-900">
                {{ subscription.days_remaining || 0 }} дн.
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Today's Appointments -->
    <div class="card">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Записи на сегодня</h2>
      <div v-if="dashboard.today?.appointments?.length" class="space-y-3">
        <div
          v-for="appointment in dashboard.today.appointments"
          :key="appointment.id"
          class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
        >
          <div>
            <p class="font-medium text-gray-900">{{ appointment.title }}</p>
            <p class="text-sm text-gray-500">{{ appointment.client_name }}</p>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">
              {{ formatTime(appointment.start_time) }}
            </span>
            <span :class="getStatusBadge(appointment.status)">
              {{ getStatusText(appointment.status) }}
            </span>
          </div>
        </div>
      </div>
      <p v-else class="text-gray-500 text-center py-8">
        Записей на сегодня нет
      </p>
    </div>

    <!-- Upcoming Appointments -->
    <div class="card">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Предстоящие записи</h2>
      <div v-if="dashboard.upcoming?.length" class="space-y-3">
        <div
          v-for="appointment in dashboard.upcoming"
          :key="appointment.id"
          class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
        >
          <div>
            <p class="font-medium text-gray-900">{{ appointment.title }}</p>
            <p class="text-sm text-gray-500">{{ appointment.client_name }}</p>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">
              {{ formatDate(appointment.start_time) }}
            </span>
            <router-link
              :to="`/appointments/${appointment.id}/edit`"
              class="text-indigo-600 hover:text-indigo-900 text-sm"
            >
              Редактировать
            </router-link>
          </div>
        </div>
      </div>
      <p v-else class="text-gray-500 text-center py-8">
        Предстоящих записей нет
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { dashboardApi } from '@/api/dashboard';
import { masterProfileApi } from '@/api/masterProfile';
import QrcodeVue from 'qrcode.vue';

const dashboard = ref({});
const subscription = ref({});
const masterProfile = ref(null);
const loading = ref(true);
const linkCopied = ref(false);

const bookingUrl = computed(() => {
  if (!masterProfile.value?.slug) return '';
  return `${window.location.origin}/book/${masterProfile.value.slug}`;
});

const fetchDashboard = async () => {
  try {
    const data = await dashboardApi.getDashboard();
    dashboard.value = data.dashboard;
    subscription.value = data.subscription;
    
    // Fetch master profile
    try {
      const profileData = await masterProfileApi.getProfile();
      masterProfile.value = profileData.profile;
    } catch (err) {
      console.error('Failed to fetch master profile:', err);
    }
  } catch (error) {
    console.error('Failed to fetch dashboard:', error);
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
  }).format(value);
};

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('ru-RU', {
    hour: '2-digit',
    minute: '2-digit',
  });
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU', {
    day: 'numeric',
    month: 'long',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getStatusBadge = (status) => {
  const badges = {
    scheduled: 'badge-info',
    completed: 'badge-success',
    cancelled: 'badge-danger',
  };
  return `badge ${badges[status] || 'badge-info'}`;
};

const getStatusText = (status) => {
  const texts = {
    scheduled: 'Запланировано',
    completed: 'Завершено',
    cancelled: 'Отменено',
  };
  return texts[status] || status;
};

async function copyLink() {
  try {
    await navigator.clipboard.writeText(bookingUrl.value);
    linkCopied.value = true;
    setTimeout(() => {
      linkCopied.value = false;
    }, 2000);
  } catch (err) {
    const input = document.querySelector('.booking-url-input');
    input.select();
    document.execCommand('copy');
    linkCopied.value = true;
    setTimeout(() => {
      linkCopied.value = false;
    }, 2000);
  }
}

async function downloadQR() {
  try {
    const svgElement = document.querySelector('.booking-qr-code svg');
    if (!svgElement) return;
    
    const svgData = new XMLSerializer().serializeToString(svgElement);
    const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(svgBlob);
    
    const img = new Image();
    img.onload = () => {
      const canvas = document.createElement('canvas');
      canvas.width = 400;
      canvas.height = 400;
      const ctx = canvas.getContext('2d');
      
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(img, 0, 0, 400, 400);
      URL.revokeObjectURL(url);
      
      const pngUrl = canvas.toDataURL('image/png');
      const downloadLink = document.createElement('a');
      downloadLink.href = pngUrl;
      downloadLink.download = `qr-${masterProfile.value.slug}.png`;
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);
    };
    img.src = url;
  } catch (err) {
    console.error('Failed to download QR:', err);
  }
}

onMounted(() => {
  fetchDashboard();
});
</script>

<style scoped>
.booking-link-card {
  border: 2px solid #667eea;
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ff 100%);
}

.booking-link-content {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 30px;
  align-items: center;
}

.booking-link-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 15px;
}

.booking-link-row {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
}

.booking-url-input {
  flex: 1;
  background: white;
  font-family: monospace;
  font-size: 0.875rem;
}

.booking-link-hint {
  font-size: 0.875rem;
  color: #718096;
}

.booking-qr-code {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 15px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.booking-qr-code svg {
  border-radius: 8px;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 0.75rem;
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

@media (max-width: 768px) {
  .booking-link-content {
    grid-template-columns: 1fr;
  }
  
  .booking-link-row {
    flex-direction: column;
  }
  
  .booking-qr-code {
    width: 100%;
  }
}
</style>
