<template>
  <nav class="bg-blue-600 text-white px-4 py-3 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <!-- Logo -->
      <router-link to="/" class="text-xl font-bold">Mini ERP</router-link>

      <!-- Menu Desktop -->
      <ul class="hidden md:flex space-x-6">
        <li>
          <router-link to="/products" class="hover:underline">Products</router-link>
        </li>
        <li>
          <router-link to="/coupons" class="hover:underline">Coupons</router-link>
        </li>
        <li>
          <router-link to="/cart" class="hover:underline">Cart</router-link>
        </li>
        <li>
          <router-link to="/orders" class="hover:underline">Orders</router-link>
        </li>
      </ul>

      <!-- Mobile Menu Button -->
      <button class="md:hidden focus:outline-none" @click="toggleMenu">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6h16M4 12h16M4 18h16"
          />
        </svg>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div v-if="isOpen" class="md:hidden mt-3 space-y-2 bg-blue-700 p-3 rounded">
      <router-link to="/products" class="block hover:underline">Products</router-link>
      <router-link to="/coupons" class="block hover:underline">Coupons</router-link>
      <router-link to="/cart" class="block hover:underline">Cart</router-link>
      <router-link to="/orders" class="block hover:underline">Orders</router-link>
    </div>
  </nav>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";

export default defineComponent({
  name: "Navbar",
  setup() {
    const isOpen = ref(false);
    const toggleMenu = () => (isOpen.value = !isOpen.value);
    return { isOpen, toggleMenu };
  },
});
</script>
