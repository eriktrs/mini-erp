<template>
  <div class="bg-white shadow-md rounded-lg p-6 mb-8">
    <h2 class="text-xl font-semibold mb-4">
      {{ editMode ? "Edit Coupon" : "Create Coupon" }}
    </h2>
    <form @submit.prevent="$emit('submit')" class="space-y-4">
      <div>
        <label class="block text-gray-700 mb-1">Code</label>
        <input
          v-model="formData.code"
          type="text"
          class="w-full border px-3 py-2 rounded"
          placeholder="Enter coupon code"
          required
        />
      </div>
      <div>
        <label class="block text-gray-700 mb-1">Discount (%)</label>
        <input
          v-model.number="formData.discount"
          type="number"
          class="w-full border px-3 py-2 rounded"
          min="1"
          max="100"
          required
        />
      </div>
      <div>
        <label class="block text-gray-700 mb-1">Minimum Value (R$)</label>
        <input
          v-model.number="formData.min_value"
          type="number"
          class="w-full border px-3 py-2 rounded"
          required
        />
      </div>
      <div>
        <label class="block text-gray-700 mb-1">Valid Until</label>
        <input
          v-model="formData.valid_until"
          type="date"
          class="w-full border px-3 py-2 rounded"
          required
        />
      </div>
      <div class="flex gap-4">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
          {{ editMode ? "Update" : "Create" }}
        </button>
        <button
          type="button"
          v-if="editMode"
          class="bg-gray-400 text-white px-4 py-2 rounded"
          @click="$emit('cancel')"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import type { PropType } from "vue";

export default defineComponent({
  name: "CouponForm",
  props: {
    formData: {
      type: Object as PropType<{
        code: string;
        discount: number;
        minimum_value: number;
        valid_until: string;
      }>,
      required: true,
    },
    editMode: Boolean,
  },
});
</script>
