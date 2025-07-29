import api from "./api";

export interface CartItem {
  id: number;
  name: string;
  price: number;
  qty: number;
}

export interface CheckoutResponse {
  id: number;
  message: string;
}

export interface AddToCartPayload {
  product_id: number;
  qty: number;
}

const orderService = {
  async getCart(): Promise<CartItem[]> {
    const response = await api.get("/orders/cart");
    return response.data.cart;
  },
  async addToCart(payload: AddToCartPayload) {
    return (await api.post("/orders/cart", payload)).data;
  },
  async removeFromCart(id: number) {
    return (await api.delete(`/orders/cart/${id}`)).data;
  },
  async applyCoupon(coupon_code: string) {
    return (await api.post("/orders/coupon", { coupon_code })).data;
  },
  async checkout(): Promise<CheckoutResponse> {
    return (await api.post("/orders/checkout")).data;
  },
};

export default orderService;
