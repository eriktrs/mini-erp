import { ref } from "vue";
import productService from "../services/productService";
import type { Product } from "../services/productService";

export function useProducts() {
  const products = ref<Product[]>([]);

  async function fetchProducts() {
    products.value = await productService.getAll();
  }

  async function addProduct(payload: Omit<Product, "id">) {
    await productService.create(payload);
    fetchProducts();
  }

  async function editProduct(id: number, payload: Omit<Product, "id">) {
    await productService.update(id, payload);
    fetchProducts();
  }

  async function removeProduct(id: number) {
    await productService.remove(id);
    fetchProducts();
  }

  return { products, fetchProducts, addProduct, editProduct, removeProduct };
}
