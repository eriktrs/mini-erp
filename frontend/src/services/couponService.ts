import api from "./api";

export interface Coupon {
  id?: number;
  code: string;
  value: number;
  valid_until: string;
  minimum_value: number;
}

const couponService = {
  getCoupons,
};

export default couponService;

// Fetch all coupons
export async function getCoupons() {
  const response = await api.get("/coupons");
  return response.data;
}

// Create coupon
export async function createCoupon(coupon: Coupon) {
  const response = await api.post("/coupons/store", coupon);
  return response.data;
}
