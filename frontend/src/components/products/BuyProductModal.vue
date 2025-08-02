<template>
  <div
    v-if="visible"
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
  >
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4">Buy {{ product?.name }}</h2>

      <!-- Variation Selector -->
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Select Variation</label>
        <select v-model="selectedVariation" class="w-full border rounded px-3 py-2">
          <option disabled value="">Choose a variation</option>
          <option v-for="v in product.variations" :key="v.id" :value="v.id">
            {{ v.name }} (Stock: {{ v.stock }})
          </option>
        </select>
      </div>

      <!-- Quantity Input -->
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Quantity</label>
        <input
          v-model.number="quantity"
          type="number"
          min="1"
          :max="getMaxStock()"
          class="w-full border rounded px-3 py-2"
        />
      </div>

      <!-- Coupon Input -->
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Coupon (optional)</label>
        <div class="flex">
          <input
            v-model="couponCode"
            type="text"
            placeholder="Enter coupon code"
            class="flex-1 border rounded-l px-3 py-2"
          />
          <button
            @click="applyCoupon"
            class="bg-blue-500 text-white px-4 rounded-r hover:bg-blue-600"
          >
            Apply
          </button>
        </div>
        <p v-if="couponApplied" class="text-green-600 text-sm mt-1">
          Coupon applied successfully!
        </p>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-2">
        <button
          @click="$emit('close')"
          class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500"
        >
          Cancel
        </button>
        <button
          @click="confirmPurchase"
          class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
        >
          Confirm
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import orderService from "../../services/orderService";

export default defineComponent({
  name: "BuyProductModal",
  props: {
    visible: { type: Boolean, required: true },
    product: { type: Object, required: true },
  },
  emits: ["close", "purchased"],
  setup(props, { emit }) {
    const selectedVariation = ref<string | null>(null);
    const quantity = ref(1);
    const couponCode = ref("");
    const couponApplied = ref(false);

    const getMaxStock = () => {
      const variation = props.product.variations.find(
        (v: any) => v.id === selectedVariation.value
      );
      return variation ? variation.stock : 1;
    };

    const applyCoupon = async () => {
      if (!couponCode.value) return;
      try {
        await orderService.applyCoupon(couponCode.value);
        couponApplied.value = true;
      } catch (error) {
        alert("Invalid coupon code");
        couponApplied.value = false;
      }
    };

    const confirmPurchase = async () => {
      if (!selectedVariation.value) {
        alert("Please select a variation");
        return;
      }
      try {
        await orderService.addToCart({
          product_id: props.product.id,
          variation_id: Number(selectedVariation.value),
          qty: quantity.value,
        });
        alert("Product added to cart!");
        emit("purchased");
        emit("close");
      } catch (error: any) {
        alert(error.response?.data?.message || "Failed to add product");
      }
    };

    return {
      selectedVariation,
      quantity,
      couponCode,
      couponApplied,
      applyCoupon,
      confirmPurchase,
      getMaxStock,
    };
  },
});
</script>
