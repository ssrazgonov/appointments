<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Отчеты</h1>

    <!-- Month Selector -->
    <div class="card">
      <div class="flex items-center space-x-4">
        <button @click="previousMonth" class="btn btn-secondary">← Назад</button>
        <span class="text-lg font-medium">
          {{ currentMonthName }} {{ currentYear }}
        </span>
        <button @click="nextMonth" class="btn btn-secondary">Вперед →</button>
      </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="card">
        <h3 class="text-sm font-medium text-gray-500">Всего записей</h3>
        <p class="text-2xl font-semibold text-gray-900 mt-2">
          {{ report.summary?.total_appointments || 0 }}
        </p>
      </div>

      <div class="card">
        <h3 class="text-sm font-medium text-gray-500">Завершено</h3>
        <p class="text-2xl font-semibold text-green-600 mt-2">
          {{ report.summary?.completed || 0 }}
        </p>
      </div>

      <div class="card">
        <h3 class="text-sm font-medium text-gray-500">Отменено</h3>
        <p class="text-2xl font-semibold text-red-600 mt-2">
          {{ report.summary?.cancelled || 0 }}
        </p>
      </div>

      <div class="card">
        <h3 class="text-sm font-medium text-gray-500">Доход</h3>
        <p class="text-2xl font-semibold text-indigo-600 mt-2">
          {{ formatCurrency(report.summary?.total_revenue || 0) }}
        </p>
      </div>
    </div>

    <!-- Daily Revenue Chart (Simple Table) -->
    <div class="card">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Доход по дням</h2>
      <div class="grid grid-cols-7 gap-2">
        <div
          v-for="(revenue, day) in report.daily_revenue"
          :key="day"
          :class="[
            'p-3 rounded-lg text-center',
            revenue > 0 ? 'bg-green-100' : 'bg-gray-100',
          ]"
        >
          <div class="text-xs text-gray-500">{{ day }}</div>
          <div class="text-sm font-medium">
            {{ formatCurrency(revenue) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Average Check -->
    <div class="card">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Статистика</h2>
      <div class="grid grid-cols-2 gap-6">
        <div>
          <h3 class="text-sm font-medium text-gray-500">Средний чек</h3>
          <p class="text-xl font-semibold text-gray-900 mt-2">
            {{ formatCurrency(report.summary?.average_check || 0) }}
          </p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Процент завершения</h3>
          <p class="text-xl font-semibold text-gray-900 mt-2">
            {{ report.summary?.total_appointments > 0 
              ? Math.round((report.summary.completed / report.summary.total_appointments) * 100) 
              : 0 }}%
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { reportService } from '@/api/reports';

const currentDate = ref(new Date());
const report = ref({});

const currentYear = computed(() => currentDate.value.getFullYear());
const currentMonth = computed(() => currentDate.value.getMonth() + 1);
const currentMonthName = computed(() => {
  const months = [
    'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
  ];
  return months[currentMonth.value - 1];
});

const loadReport = async () => {
  try {
    report.value = await reportService.getMonthlyReport(currentYear.value, currentMonth.value);
  } catch (error) {
    console.error('Failed to load report:', error);
  }
};

const previousMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value - 2, 1);
  loadReport();
};

const nextMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value, 1);
  loadReport();
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
  }).format(value);
};

onMounted(() => {
  loadReport();
});
</script>
