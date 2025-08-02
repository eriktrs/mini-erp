<template>
  <div class="p-6 max-w-5xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">My Orders</h2>

    <!-- Filters -->
    <div class="flex justify-between items-center mb-6">
      <select
        v-model="statusFilter"
        @change="fetchOrders"
        class="border px-3 py-2 rounded"
      >
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="processing">Processing</option>
        <option value="completed">Completed</option>
        <option value="canceled">Canceled</option>
      </select>
    </div>

    <!-- Loading / Empty State -->
    <div v-if="loading" class="text-gray-500">Loading orders...</div>
    <div v-else-if="orders.length === 0" class="text-gray-500">No orders found.</div>

    <!-- Orders List -->
    <div v-else class="space-y-6">
      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white shadow rounded-lg p-4 border"
      >
        <!-- Header -->
        <div class="flex justify-between items-center mb-2">
          <h3 class="font-semibold text-lg">Order #{{ order.id }}</h3>
          <span
            class="px-3 py-1 text-sm rounded-full capitalize"
            :class="getStatusClass(order.status)"
          >
            {{ order.status }}
          </span>
        </div>

        <!-- Details -->
        <p class="text-sm text-gray-600">Placed on: {{ formatDate(order.created_at) }}</p>
        <p class="text-sm text-gray-600 mb-3">Address: {{ order.address }}</p>

        <!-- Items -->
        <div v-if="order.items && order.items.length">
          <h4 class="font-semibold mb-1">Items:</h4>
          <ul class="space-y-1">
            <li
              v-for="item in order.items"
              :key="item.product_name + (item.variation_name || '')"
              class="text-gray-700"
            >
              {{ item.product_name }}
              <span v-if="item.variation_name" class="text-gray-500">
                ({{ item.variation_name }})
              </span>
              - Qty: {{ item.quantity }} - R$
              {{ (item.price * item.quantity).toFixed(2) }}
            </li>
          </ul>
        </div>
        <div v-else class="text-gray-500 text-sm italic">
          No item details available for this order.
        </div>

        <!-- Total -->
        <div class="flex justify-end mt-3 font-bold text-green-600">
          Total: R$ {{ Number(order.total).toFixed(2) }}
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center items-center gap-4 mt-6">
        <button
          :disabled="pagination.current_page === 1"
          @click="changePage(pagination.current_page - 1)"
          class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
        >
          Previous
        </button>
        <span> Page {{ pagination.current_page }} of {{ pagination.total_pages }} </span>
        <button
          :disabled="pagination.current_page === pagination.total_pages"
          @click="changePage(pagination.current_page + 1)"
          class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "../services/api";

const orders = ref<any[]>([]);
const loading = ref(true);

// Filters and Pagination
const statusFilter = ref("");
const pagination = ref({
  current_page: 1,
  total_pages: 1,
  total_orders: 0,
  limit: 4,
});

async function fetchOrders(page = pagination.value.current_page) {
  loading.value = true;
  try {
    const { data } = await api.get("/orders", {
      params: {
        status: statusFilter.value || undefined,
        page,
        limit: pagination.value.limit,
      },
    });

    orders.value = (data.orders || []).map((order: any) => ({
      ...order,
      subtotal: Number(order.subtotal),
      shipping: Number(order.shipping),
      total: Number(order.total),
      items: order.items || [],
    }));

    pagination.value.current_page = data.pagination.current_page;
    pagination.value.total_pages = data.pagination.total_pages;
    pagination.value.total_orders = data.pagination.total_orders;
  } catch (error) {
    console.error("Failed to fetch orders", error);
  } finally {
    loading.value = false;
  }
}

function changePage(page: number) {
  if (page < 1 || page > pagination.value.total_pages) return;
  fetchOrders(page);
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString("pt-BR", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

function getStatusClass(status: string) {
  switch (status.toLowerCase()) {
    case "processing":
      return "bg-yellow-100 text-yellow-700";
    case "completed":
      return "bg-green-100 text-green-700";
    case "canceled":
      return "bg-red-100 text-red-700";
    default:
      return "bg-gray-100 text-gray-700";
  }
}

onMounted(() => fetchOrders());
</script>
