<template>
  <div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Coupons List</h2>
    <table class="min-w-full border-collapse">
      <thead>
        <tr class="bg-gray-100 text-gray-700">
          <th class="px-4 py-2 border">Code</th>
          <th class="px-4 py-2 border">Discount</th>
          <th class="px-4 py-2 border">Min Value</th>
          <th class="px-4 py-2 border">Valid Until</th>
          <th class="px-4 py-2 border">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="coupon in coupons" :key="coupon.id" class="hover:bg-gray-50">
          <td class="border px-4 py-2">{{ coupon.code }}</td>
          <td class="border px-4 py-2">{{ coupon.discount }}%</td>
          <td class="border px-4 py-2">R$ {{ coupon.min_value }}</td>
          <td class="border px-4 py-2">{{ coupon.valid_until }}</td>
          <td class="border px-4 py-2">
            <button @click="$emit('edit', coupon)" class="text-blue-600 hover:underline mr-2">Edit</button>
            <button @click="$emit('delete', coupon.id)" class="text-red-600 hover:underline">Delete</button>
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
  name: "CouponTable",
  props: {
    coupons: {
      type: Array as PropType<
        { id: number; code: string; discount: number; min_value: number; valid_until: string }[]
      >,
      required: true,
    },
  },
});
</script>
