import api from "./api";

export interface Product {
  id: number;
  name: string;
  price: number;
  variations?: string[];
  stock?: number;
}

const productService = {
  async getAll(): Promise<Product[]> {
    const response = await api.get("/products");
    return response.data.data;
  },
  async create(payload: Omit<Product, "id">) {
    return (await api.post("/products", payload)).data;
  },
  async update(id: number, payload: Omit<Product, "id">) {
    return (await api.put(`/products/${id}`, payload)).data;
  },
  async remove(id: number) {
    return (await api.delete(`/products/${id}`)).data;
  },
};

export default productService;
