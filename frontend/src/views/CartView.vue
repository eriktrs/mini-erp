<template>
  <div class="p-6 max-w-3xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Checkout</h2>

    <!-- Cart Items -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <h3 class="text-lg font-semibold mb-4">Your Cart</h3>
      <div v-if="cartItems.length" class="space-y-3">
        <div
          v-for="item in cartItems"
          :key="item.id"
          class="flex justify-between items-center border-b pb-2"
        >
          <div>
            <!-- Product Name -->
            <span class="block font-semibold">{{ item.product_name }}</span>

            <!-- Variation Name -->
            <small v-if="item.variation_name" class="block text-gray-500">
              {{ item.variation_name }}
            </small>

            <!-- Quantity -->
            <small class="text-gray-500">Quantity:</small>
            <input
              type="number"
              min="1"
              v-model.number="item.qty"
              @change="updateItem(item)"
              class="border rounded px-2 py-1 w-16 ml-2"
            />
          </div>

          <!-- Price + Remove Button -->
          <div class="flex items-center gap-4">
            <span class="text-gray-800">
              R$ {{ (item.price * item.qty).toFixed(2) }}
            </span>
            <button @click="removeItem(item.id)" class="text-red-500 hover:text-red-700">
              ✕
            </button>
          </div>
        </div>
      </div>
      <div v-else class="text-gray-500 text-center">No items in cart</div>
    </div>

    <!-- Coupon -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <h3 class="text-lg font-semibold mb-2">Apply Coupon</h3>
      <div class="flex gap-2">
        <input
          v-model="couponCode"
          placeholder="Enter coupon code"
          class="border rounded px-3 py-2 w-full"
        />
        <button
          @click="applyCoupon"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
        >
          Apply
        </button>
      </div>
    </div>

    <!-- Address -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
      <div class="space-y-3">
        <input
          v-model="postal_code"
          placeholder="Postal Code"
          @blur="fetchAddress"
          class="border rounded px-3 py-2 w-full"
        />
        <input
          v-model="address"
          placeholder="Address"
          class="border rounded px-3 py-2 w-full"
        />
      </div>
    </div>

    <!-- Summary -->
    <div class="bg-gray-100 shadow rounded-lg p-4">
      <div class="flex justify-between font-semibold mb-2">
        <span>Subtotal:</span>
        <span>R$ {{ subtotal.toFixed(2) }}</span>
      </div>

      <!-- Coupon applied -->
      <div v-if="appliedCoupon" class="flex justify-between text-green-600 mb-2">
        <span>Discount ({{ appliedCoupon.code }}):</span>
        <span>- R$ {{ appliedCoupon.discount.toFixed(2) }}</span>
      </div>

      <div class="flex justify-between mb-2">
        <span>Shipping:</span>
        <span>R$ {{ shipping.toFixed(2) }}</span>
      </div>

      <div class="flex justify-between font-bold text-green-600 mb-4">
        <span>Total:</span>
        <span>R$ {{ total.toFixed(2) }}</span>
      </div>

      <button
        @click="checkoutOrder"
        class="bg-blue-600 text-white px-4 py-2 rounded w-full hover:bg-blue-700"
      >
        Finalize Order
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "../services/api";

const cartItems = ref<any[]>([]);
const subtotal = ref(0);
const shipping = ref(0);
const total = ref(0);
const couponCode = ref("");
const appliedCoupon = ref<{ code: string; discount: number } | null>(null);
const postal_code = ref("");
const address = ref("");

// Fetch cart data from API
async function fetchCart() {
  const { data } = await api.get("/orders/cart");
  cartItems.value = data.cart.map((item: any) => ({
    id: item.id,
    product_name: item.product_name,
    variation_name: item.variation_name || "",
    price: parseFloat(item.price),
    qty: item.quantity,
  }));
  subtotal.value = data.subtotal;
  shipping.value = data.shipping;
  total.value = data.total;

  // Check if coupon exists
  if (data.coupon) {
    appliedCoupon.value = {
      code: data.coupon.code,
      discount: parseFloat(data.discount),
    };
  } else {
    appliedCoupon.value = null;
  }
}

// Remove item
async function removeItem(id: number) {
  await api.delete(`/orders/cart/${id}`);
  await fetchCart();
}

// Update quantity
async function updateItem(item: any) {
  await api.put(`/orders/cart/${item.id}`, { qty: item.qty });
  await fetchCart();
}

// Apply coupon
async function applyCoupon() {
  if (!couponCode.value) return alert("Please enter a coupon code");
  try {
    await api.post("/orders/coupon", { coupon_code: couponCode.value });
    await fetchCart();
    alert("Coupon applied successfully!");
  } catch (error: any) {
    alert(error.response?.data?.message || "Failed to apply coupon");
  }
}

// Fetch address from ViaCEP
async function fetchAddress() {
  if (!postal_code.value) return;
  const cep = postal_code.value.replace(/\D/g, "");
  if (cep.length !== 8) {
    alert("Invalid postal code format");
    return;
  }
  try {
    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
    if (!response.ok) throw new Error();
    const data = await response.json();
    if (data.erro) {
      alert("Postal code not found.");
      return;
    }
    address.value = `${data.logradouro || ""}, ${data.bairro || ""}, ${
      data.localidade || ""
    } - ${data.uf || ""}`.trim();
  } catch {
    alert("Failed to fetch address.");
  }
}

// Checkout order
async function checkoutOrder() {
  if (!postal_code.value || !address.value) {
    return alert("Please fill in your address");
  }
  try {
    const { data } = await api.post("/orders/checkout", {
      postal_code: postal_code.value,
      address: address.value,
    });
    alert(`Order completed! ID: ${data.order_id}`);
    await fetchCart();
  } catch (error: any) {
    alert(error.response?.data?.message || "Checkout failed");
  }
}

onMounted(fetchCart);
</script>
