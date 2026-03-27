<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Подписка</h1>

    <!-- Current Subscription Status -->
    <div class="card">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Текущий статус</h2>
      <div v-if="subscription.has_subscription" class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">План</p>
            <p class="text-xl font-semibold text-gray-900">{{ subscription.plan_name }}</p>
          </div>
          <div>
            <span :class="subscription.status === 'active' ? 'badge-success' : 'badge-danger'" class="badge">
              {{ subscription.status === 'active' ? 'Активна' : 'Истекла' }}
            </span>
          </div>
        </div>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Действует до</p>
            <p class="text-lg font-medium text-gray-900">{{ formatDate(subscription.ends_at) }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Осталось дней</p>
            <p class="text-lg font-medium text-indigo-600">{{ subscription.days_remaining }}</p>
          </div>
        </div>
      </div>
      <div v-else class="text-gray-500 text-center py-8">
        У вас нет активной подписки
      </div>
    </div>

    <!-- Available Plans -->
    <div class="card">
      <h2 class="text-lg font-medium text-gray-900 mb-6">Доступные планы</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="plan in plans"
          :key="plan.id"
          :class="[
            'border-2 rounded-lg p-6',
            selectedPlan === plan.id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200',
          ]"
        >
          <h3 class="text-xl font-bold text-gray-900">{{ plan.name }}</h3>
          <p class="text-sm text-gray-500 mt-2">{{ plan.description }}</p>
          <div class="mt-4">
            <span class="text-3xl font-bold text-gray-900">{{ formatCurrency(plan.price) }}</span>
            <span class="text-gray-500">/{{ plan.duration_days }} дн.</span>
          </div>
          <ul class="mt-4 space-y-2">
            <li v-for="feature in plan.features" :key="feature" class="text-sm text-gray-600 flex items-center">
              <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              {{ feature }}
            </li>
          </ul>
          <button
            @click="selectPlan(plan.id)"
            :disabled="processing"
            class="btn btn-primary w-full mt-6"
          >
            {{ processing ? 'Обработка...' : 'Выбрать план' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Payment History -->
    <div class="card">
      <h2 class="text-lg font-medium text-gray-900 mb-4">История платежей</h2>
      <div v-if="payments.data?.length" class="space-y-3">
        <div
          v-for="payment in payments.data"
          :key="payment.id"
          class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
        >
          <div>
            <p class="font-medium text-gray-900">{{ payment.subscription_plan?.name || 'Подписка' }}</p>
            <p class="text-sm text-gray-500">{{ formatDate(payment.created_at) }}</p>
          </div>
          <div class="flex items-center space-x-4">
            <span :class="getPaymentStatusBadge(payment.status)">
              {{ getPaymentStatusText(payment.status) }}
            </span>
            <span class="font-medium">{{ formatCurrency(payment.amount) }}</span>
          </div>
        </div>
      </div>
      <p v-else class="text-gray-500 text-center py-8">
        Платежей еще не было
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { subscriptionsApi } from '@/api/subscriptions';
import { paymentsApi } from '@/api/payments';

const subscription = ref({});
const plans = ref([]);
const payments = ref({ data: [] });
const selectedPlan = ref(null);
const processing = ref(false);

const loadSubscription = async () => {
  try {
    subscription.value = await subscriptionsApi.getCurrentSubscription();
  } catch (error) {
    console.error('Failed to load subscription:', error);
  }
};

const loadPlans = async () => {
  try {
    plans.value = await subscriptionsApi.getPlans();
  } catch (error) {
    console.error('Failed to load plans:', error);
  }
};

const loadPayments = async () => {
  try {
    payments.value = await paymentsApi.getPayments({ per_page: 10 });
  } catch (error) {
    console.error('Failed to load payments:', error);
  }
};

const selectPlan = async (planId) => {
  processing.value = true;
  try {
    const result = await paymentsApi.createPayment(planId);
    if (result.payment_url) {
      window.location.href = result.payment_url;
    }
  } catch (error) {
    console.error('Failed to create payment:', error);
    alert('Ошибка при создании платежа');
  } finally {
    processing.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '—';
  return new Date(dateString).toLocaleDateString('ru-RU');
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
  }).format(value);
};

const getPaymentStatusBadge = (status) => {
  const badges = {
    pending: 'badge-warning',
    paid: 'badge-success',
    failed: 'badge-danger',
  };
  return `badge ${badges[status] || 'badge-info'}`;
};

const getPaymentStatusText = (status) => {
  const texts = {
    pending: 'Ожидается',
    paid: 'Оплачен',
    failed: 'Отклонен',
  };
  return texts[status] || status;
};

onMounted(() => {
  loadSubscription();
  loadPlans();
  loadPayments();
});
</script>
