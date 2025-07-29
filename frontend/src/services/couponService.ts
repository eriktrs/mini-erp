import api from "./api";

export interface Coupon {
  id: number;
  code: string;
  discount: number;
  min_value: number;
  valid_until: string;
}

const couponService = {
  async getAll(): Promise<Coupon[]> {
    return (await api.get("/coupons")).data.coupons;
  },
  async create(payload: Omit<Coupon, "id">) {
    return (await api.post("/coupons", payload)).data;
  },
  async update(id: number, payload: Omit<Coupon, "id">) {
    return (await api.put(`/coupons/${id}`, payload)).data;
  },
  async remove(id: number) {
    return (await api.delete(`/coupons/${id}`)).data;
  },
};

export default couponService;
