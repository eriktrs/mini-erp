<template>
  <div class="bg-white shadow-md rounded p-4 mb-6 max-w-lg mx-auto">
    <h2 class="text-lg font-semibold mb-4">
      {{ editMode ? "Edit Product" : "Create Product" }}
    </h2>
    <form @submit.prevent="onSubmit" class="space-y-4">
      <!-- Product Name -->
      <div>
        <label class="block text-gray-700 mb-1">Name</label>
        <input
          v-model="localForm.name"
          type="text"
          required
          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <!-- Product Price -->
      <div>
        <label class="block text-gray-700 mb-1">Price</label>
        <input
          v-model.number="localForm.price"
          type="number"
          step="0.01"
          min="0"
          required
          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <!-- Variations Section -->
      <div>
        <label class="block text-gray-700 mb-2">Variations</label>

        <div
          v-for="(variation, index) in localForm.variations"
          :key="index"
          class="flex items-center gap-2 mb-2"
        >
          <input
            v-model="variation.name"
            type="text"
            placeholder="Variation name (e.g., Size 42)"
            required
            class="flex-1 border rounded px-3 py-2"
          />
          <input
            v-model.number="variation.stock"
            type="number"
            placeholder="Stock"
            min="0"
            required
            class="w-24 border rounded px-3 py-2"
          />
          <button
            type="button"
            @click="removeVariation(index)"
            class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600"
            aria-label="Remove variation"
          >
            ✕
          </button>
        </div>

        <button
          type="button"
          @click="addVariation"
          class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600"
        >
          + Add Variation
        </button>
      </div>

      <!-- Actions -->
      <div class="flex justify-between mt-4">
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
    const localForm = reactive({
      name: "",
      price: 0,
      variations: [] as { name: string; stock: number }[],
      ...props.formData,
    });

    watch(
      () => props.formData,
      (newVal) => {
        Object.assign(localForm, newVal);
      }
    );

    const addVariation = () => {
      localForm.variations.push({ name: "", stock: 0 });
    };

    const removeVariation = (index: number) => {
      localForm.variations.splice(index, 1);
    };

    const onSubmit = () => {
      if (localForm.variations.length === 0) {
        alert("Please add at least one variation with stock.");
        return;
      }
      emit("submit", { ...localForm });
    };

    return { localForm, addVariation, removeVariation, onSubmit };
  },
});
</script>
