import { ref } from "vue";
import productService from "../services/productService";
import type { Product, Variation } from "../services/productService";

export function useProducts() {
  const products = ref<Product[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function fetchProducts() {
    loading.value = true;
    error.value = null;
    try {
      products.value = await productService.getAll();
    } catch (err: any) {
      error.value = "Failed to load products";
      console.error(err);
    } finally {
      loading.value = false;
    }
  }

  async function addProduct(payload: { name: string; price: number; variations: Variation[] }) {
    try {
      await productService.create(payload);
    } catch (err) {
      console.error("Error creating product:", err);
      throw err;
    }
  }

  async function editProduct(id: number, payload: { name: string; price: number; variations: Variation[] }) {
    try {
      await productService.update(id, payload);
    } catch (err) {
      console.error("Error updating product:", err);
      throw err;
    }
  }

  async function removeProduct(id: number) {
    try {
      await productService.remove(id);
    } catch (err) {
      console.error("Error deleting product:", err);
      throw err;
    }
  }

  return {
    products,
    loading,
    error,
    fetchProducts,
    addProduct,
    editProduct,
    removeProduct,
  };
}
