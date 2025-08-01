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
        @buy="buyProduct"
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
import orderService from "../services/orderService";

const { products, fetchProducts, addProduct, editProduct, removeProduct } = useProducts();

const loading = ref(true);
const showForm = ref(false);
const selectedProduct = ref<any>(null);

const defaultForm = { name: "", price: 0, variations: [] };

onMounted(async () => {
  await fetchProducts();
  loading.value = false;
});

function openForm(product: any = null) {
  if (product) {
    selectedProduct.value = {
      ...product,
      variations: product.variations || [],
    };
  } else {
    selectedProduct.value = null;
  }
  showForm.value = true;
}

function closeForm() {
  showForm.value = false;
  selectedProduct.value = null;
}

async function saveProduct(data: any) {
  if (selectedProduct.value) {
    await editProduct(selectedProduct.value.id, data);
  } else {
    await addProduct(data);
  }
  await fetchProducts();
  closeForm();
}

async function deleteProduct(id: number) {
  if (confirm("Are you sure you want to delete this product?")) {
    await removeProduct(id);
    await fetchProducts();
  }
}

async function buyProduct(product: any) {
  if (!product.variations || product.variations.length === 0) {
    alert("This product has no variations.");
    return;
  }

  const variation = product.variations[0];

  try {
    await orderService.addToCart({
      product_id: product.id,
      variation_id: variation.id,
      qty: 1
    });

    alert("Product added to cart!");
    
    await fetchProducts();

  } catch (error) {
    alert("Failed to add product to cart. Maybe no stock left.");
  }
}
</script>
