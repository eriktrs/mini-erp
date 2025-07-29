<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Order</h2>
    <OrderSummary :items="cartItems" :shipping="shippingCost" @checkout="handleCheckout" />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from "vue";
import OrderSummary from "../components/orders/OrderSummary.vue";
import { useOrders } from "../composables/useOrders";

const { cartItems, fetchCart, checkout } = useOrders();
const shippingCost = computed(() => 20);

async function handleCheckout() {
  const response = await checkout();
  alert(`Order completed! ID: ${response.id}`);
}

onMounted(fetchCart);
</script>
