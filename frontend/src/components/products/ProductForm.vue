<template>
  <div class="bg-white shadow-md rounded p-4 mb-6 max-w-lg mx-auto">
    <h2 class="text-lg font-semibold mb-4">
      {{ editMode ? "Edit Product" : "Create Product" }}
    </h2>
    <form @submit.prevent="onSubmit" class="space-y-4">
      <div>
        <label class="block text-gray-700 mb-1">Name</label>
        <input
          v-model="localForm.name"
          type="text"
          required
          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <div>
        <label class="block text-gray-700 mb-1">Price</label>
        <input
          v-model.number="localForm.price"
          type="number"
          step="0.01"
          required
          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <div>
        <label class="block text-gray-700 mb-1">Variations</label>
        <input
          v-model="localForm.variations"
          type="text"
          placeholder="e.g. Size: M, Color: Red"
          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <div>
        <label class="block text-gray-700 mb-1">Stock</label>
        <input
          v-model.number="localForm.stock"
          type="number"
          required
          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <div class="flex justify-between">
        <button
          type="submit"
          class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
          {{ editMode ? "Update" : "Create" }}
        </button>
        <button
          type="button"
          @click="$emit('cancel')"
          class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, watch } from "vue";

export default defineComponent({
  name: "ProductForm",
  props: {
    formData: { type: Object, required: true },
    editMode: { type: Boolean, default: false },
  },
  emits: ["submit", "cancel"],
  setup(props, { emit }) {
    const localForm = reactive({ ...props.formData });

    watch(
      () => props.formData,
      (newVal) => {
        Object.assign(localForm, newVal);
      }
    );

    const onSubmit = () => {
      emit("submit", { ...localForm });
    };

    return { localForm, onSubmit };
  },
});
</script>
