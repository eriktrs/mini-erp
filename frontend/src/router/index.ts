import { createRouter, createWebHistory } from "vue-router";
import ProductsView from "../views/ProductsView.vue";
import CouponsView from "../views/CouponsView.vue";
import OrdersView from "../views/OrdersView.vue";
import CartView from "../views/CartView.vue";

const routes = [
  { path: "/", redirect: "/products" },
  { path: "/products", component: ProductsView },
  { path: "/coupons", component: CouponsView },
  { path: "/orders", component: OrdersView },
  { path: "/cart", component: CartView },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
