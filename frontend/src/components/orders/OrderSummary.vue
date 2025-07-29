<template>
  <div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
    <div v-if="items.length" class="space-y-3">
      <div v-for="item in items" :key="item.id" class="flex justify-between">
        <span>{{ item.name }} (x{{ item.qty }})</span>
        <span>R$ {{ (item.price * item.qty).toFixed(2) }}</span>
      </div>
      <hr />
      <div class="flex justify-between font-bold">
        <span>Subtotal:</span>
        <span>R$ {{ subtotal.toFixed(2) }}</span>
      </div>
      <div class="flex justify-between">
        <span>Shipping:</span>
        <span>R$ {{ shipping.toFixed(2) }}</span>
      </div>
      <div class="flex justify-between font-bold text-green-600">
        <span>Total:</span>
        <span>R$ {{ total.toFixed(2) }}</span>
      </div>
      <button @click="$emit('checkout')" class="bg-blue-600 text-white px-4 py-2 rounded w-full mt-4">
        Finalize Order
      </button>
    </div>
    <div v-else class="text-gray-500 text-center">No items in cart</div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import type { PropType } from "vue";

export default defineComponent({
  name: "OrderSummary",
  props: {
    items: {
      type: Array as PropType<{ id: number; name: string; price: number; qty: number }[]>,
      default: () => [],
    },
    shipping: {
      type: Number,
      default: 0,
    },
  },
  setup(props) {
    const subtotal = computed(() =>
      props.items.reduce((sum, item) => sum + item.price * item.qty, 0)
    );
    const total = computed(() => subtotal.value + props.shipping);
    return { subtotal, total };
  },
});
</script>
