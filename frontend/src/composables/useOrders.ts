import { ref } from "vue";
import orderService from "../services/orderService";
import type { CartItem, CheckoutResponse } from "../services/orderService";

export function useOrders() {
  const cartItems = ref<CartItem[]>([]);

  async function fetchCart() {
    cartItems.value = await orderService.getCart();
  }

  async function checkout(): Promise<CheckoutResponse> {
    return await orderService.checkout();
  }

  return { cartItems, fetchCart, checkout };
}
