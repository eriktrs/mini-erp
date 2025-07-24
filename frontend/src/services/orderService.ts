import axios from "axios";

/**
 * Define API base URL from .env (default: localhost)
 */
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "http://localhost:8000",
});

/**
 * Order interface to type responses
 */
export interface Order {
  id: number;
  customer_name?: string;
  subtotal: string;
  shipping: string;
  total: string;
  status: string;
  created_at: string;
}

export default {
  /**
   * Sends the order data to the backend to place an order.
   * @param orderData Object containing customer and order details
   */
  async placeOrder(orderData: Record<string, any>): Promise<void> {
    try {
      // POST request to /order/place endpoint (ajuste conforme sua rota)
      await api.post('/order/place', orderData);
    } catch (error) {
      // You can customize error handling here
      throw new Error('Error placing order');
    }
  }
}

/**
 * Fetch all orders
 */
export const getOrders = async (): Promise<Order[]> => {
  const response = await api.get("/orders");
  return response.data;
};

/**
 * Update order status
 * @param id Order ID
 * @param status New status
 */
export const updateOrderStatus = async (
  id: number,
  status: string
): Promise<void> => {
  await api.post(`/orders/update/${id}`, { status });
};

/**
 * Cancel order using Webhook endpoint
 * @param id Order ID
 */
export const cancelOrderByWebhook = async (id: number): Promise<void> => {
  await api.post("/webhook/order-status", {
    order_id: id,
    status: "canceled",
  });
};

/**
 * Create a new order (Checkout)
 */
export const createOrder = async (orderData: {
  products: { product_id: number; variation_id?: number; quantity: number }[];
  address: string;
  postal_code: string;
  subtotal: number;
  shipping: number;
  total: number;
  coupon_code?: string;
}): Promise<{ message: string; order_id: number }> => {
  const response = await api.post("/order/place", orderData);
  return response.data;
};

