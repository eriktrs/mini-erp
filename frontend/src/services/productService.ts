import axios from 'axios'

const API_BASE = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

export interface Product {
  id: number
  name: string
  price: number
}

export async function getProducts(): Promise<Product[]> {
  const response = await axios.get(`${API_BASE}/products`)
  return response.data
}

export async function createProduct(product: Partial<Product>) {
  const response = await axios.post(`${API_BASE}/products/store`, product)
  return response.data
}

export async function updateProduct(id: number, product: Partial<Product>) {
  const response = await axios.post(`${API_BASE}/products/update/${id}`, product)
  return response.data
}

export async function deleteProduct(id: number) {
  const response = await axios.delete(`${API_BASE}/products/delete/${id}`)
  return response.data
}
