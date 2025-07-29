<template>
  <div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Products List</h2>
    <table class="min-w-full table-auto border-collapse">
      <thead>
        <tr class="bg-gray-100 text-gray-700">
          <th class="px-4 py-2 border">ID</th>
          <th class="px-4 py-2 border">Name</th>
          <th class="px-4 py-2 border">Price (R$)</th>
          <th class="px-4 py-2 border">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="product in products"
          :key="product.id"
          class="hover:bg-gray-50"
        >
          <td class="px-4 py-2 border">{{ product.id }}</td>
          <td class="px-4 py-2 border">{{ product.name }}</td>
          <td class="px-4 py-2 border">{{ product.price.toFixed(2) }}</td>
          <td class="px-4 py-2 border text-center">
            <button
              @click="$emit('edit', product)"
              class="text-blue-600 hover:underline mr-2"
            >
              Edit
            </button>
            <button
              @click="$emit('delete', product.id)"
              class="text-red-600 hover:underline"
            >
              Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import type { PropType } from "vue";

export default defineComponent({
  name: "ProductTable",
  props: {
    products: {
      type: Array as PropType<
        { id: number; name: string; price: number; variations?: any[] }[]
      >,
      required: true,
    },
  },
});
</script>
