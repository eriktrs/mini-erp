<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Products Management</h1>

    <div class="mb-4 flex justify-between">
      <button
        @click="openForm()"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Add Product
      </button>
    </div>

    <div v-if="loading" class="text-gray-500 text-center">Loading...</div>
    <div v-else>
      <ProductTable
        v-if="products && products.length > 0"
        :products="products"
        @edit="openForm"
        @delete="deleteProduct"
      />
      <p v-else class="text-gray-500 text-center">No products found</p>
    </div>

    <Modal v-if="showForm" @close="closeForm">
      <ProductForm
        :formData="selectedProduct || defaultForm"
        :editMode="!!selectedProduct"
        @submit="saveProduct"
        @cancel="closeForm"
      />
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import ProductTable from "../components/products/ProductTable.vue";
import ProductForm from "../components/products/ProductForm.vue";
import Modal from "../components/ui/Modal.vue";
import { useProducts } from "../composables/useProducts";

const { products, fetchProducts, addProduct, editProduct, removeProduct } = useProducts();

const loading = ref(true);
const showForm = ref(false);
const selectedProduct = ref<any>(null);
const defaultForm = { name: "", price: 0, stock: 0 };

onMounted(async () => {
  await fetchProducts();
  loading.value = false;
});

function openForm(product: any = null) {
  selectedProduct.value = product;
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
}

async function saveProduct(data: any) {
  if (selectedProduct.value) {
    await editProduct(selectedProduct.value.id, data);
  } else {
    await addProduct(data);
  }
  closeForm();
}

async function deleteProduct(id: number) {
  if (confirm("Are you sure you want to delete this product?")) {
    await removeProduct(id);
  }
}
</script>
