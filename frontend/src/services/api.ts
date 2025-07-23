import axios from "axios";

const api = axios.create({
  baseURL: "http://localhost:8000/api", // Ajustar depois se necessário
});

export default api;
