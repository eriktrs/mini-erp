import api from "./api";

export interface Coupon {
  id: number | null;
  code: string;
  discount: number;
  min_value: number;
  valid_until: string;
}

const couponService = {
  /**
   * Fetch all coupons from the API.
   */
  async getAll(): Promise<Coupon[]> {
    const response = await api.get("/coupons");
    return (response.data.coupons || []).map((coupon: any) => ({
      id: coupon.id,
      code: coupon.code,
      discount: Number(coupon.value), 
      min_value: Number(coupon.minimum_value), 
      valid_until: coupon.valid_until,
    }));
  },

  /**
   * Create a new coupon.
   * Maps frontend fields to backend fields.
   */
  async create(payload: Omit<Coupon, "id">) {
    const mappedPayload = {
      code: payload.code,
      value: payload.discount,
      minimum_value: payload.min_value,
      valid_until: payload.valid_until,
    };
    return (await api.post("/coupons", mappedPayload)).data;
  },

  /**
   * Update an existing coupon by ID.
   * Maps frontend fields to backend fields.
   */
  async update(id: number, payload: Omit<Coupon, "id">) {
    const mappedPayload = {
      code: payload.code,
      value: payload.discount,
      minimum_value: payload.min_value,
      valid_until: payload.valid_until,
    };
    return (await api.put(`/coupons/${id}`, mappedPayload)).data;
  },

  /**
   * Remove a coupon by ID.
   */
  async remove(id: number) {
    return (await api.delete(`/coupons/${id}`)).data;
  },
};

export default couponService;
