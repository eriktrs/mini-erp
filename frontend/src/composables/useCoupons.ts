import { ref } from "vue";
import couponService from "../services/couponService";
import type { Coupon } from "../services/couponService";

export function useCoupons() {
  const coupons = ref<Coupon[]>([]);

  async function fetchCoupons() {
    coupons.value = await couponService.getAll();
  }

  async function createCoupon(payload: Omit<Coupon, "id">) {
    await couponService.create(payload);
    fetchCoupons();
  }

  async function updateCoupon(id: number, payload: Omit<Coupon, "id">) {
    await couponService.update(id, payload);
    fetchCoupons();
  }

  async function removeCoupon(id: number) {
    await couponService.remove(id);
    fetchCoupons();
  }

  return { coupons, fetchCoupons, createCoupon, updateCoupon, removeCoupon };
}
