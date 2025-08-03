# Mini ERP - Orders, Products, Coupons and Stock

[![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](./README.md)
[![en](https://img.shields.io/badge/lang-en-red.svg)](./README.en.md)

---

## 📌 Overview
This project is a **Mini ERP** developed in **PHP (CodeIgniter 3)** for managing **Products, Orders, Stock, and Coupons**, with **Vue.js frontend** and **Docker containerization**.  
Features:

✅ Product management with variations and stock control  
✅ Shopping cart with automatic shipping calculation  
✅ Discount coupons with minimum value rules  
✅ Order checkout with subtotal, shipping, and discount calculation  
✅ Address lookup via **ViaCEP API**  
✅ Webhook for order update/cancellation  
✅ **RESTful API** with **JSON responses** and **CORS support**  

---

## 🛠 Technologies Used
- **Backend:** PHP 7.4, CodeIgniter 3  
- **Frontend:** Vue 3 (Vite + TypeScript)  
- **Database:** MySQL  
- **Styling:** TailwindCSS  
- **Integration:** Axios, ViaCEP API  
- **Containerization:** Docker + Docker Compose  

---

## 📂 Project Structure
```
mini-erp/
├── backend/
│   ├── application/
│   │   ├── config/
│   │   ├── controllers/
│   │   ├── helpers/
│   │   ├── hooks/
│   │   ├── models/
│   │   └── views/errors
│   └── system/
├── frontend/
│   ├── src/components/
│   ├── src/views/
│   ├── src/services/
│   └── vite.config.ts
├── docker/
│   ├── apache-php.dockerfile
│   ├── node.dockerfile
│   ├── init/init_db.sql
│   └── vhost.conf
├── docker-compose.yml
└── README.md
```

---

## ⚙️ Environment Setup
1. Create a **`.env`** file in the project root based on **`.env.example`**  
2. Create a **`.env`** file inside the `frontend/` folder based on **`.env.example`**  
3. Access URLs:
   - **Backend (API):** `http://localhost:8080`
   - **Frontend (Vue):** `http://localhost:5173`

---

## 🗄 Database
Main tables:
- `products`
- `product_variations`
- `product_stock`
- `coupons`
- `orders`
- `order_items`

Initialization SQL file:  
`docker/init/init_db.sql`

---

## 🚀 How to Run the Project
### Requirements
- WSL / Linux  
- Docker and Docker Compose  
- Node.js  

### Installation
```bash
# Clone the repository
git clone https://github.com/eriktrs/mini-erp.git
cd mini-erp

# Start containers
docker-compose up -d --build
```

---

## 📡 API Endpoints

### Products
- `GET /products` → List all products  
- `POST /products` → Create a new product  

### Cart
- `GET /orders/cart` → List items in the cart  
- `POST /orders/cart` → Add item to cart  
- `PUT /orders/cart/{id}` → Update quantity  
- `DELETE /orders/cart/{id}` → Remove item  

### Coupons
- `POST /orders/coupon` → Apply discount coupon  

### Orders
- `POST /orders/checkout` → Complete checkout  

### Webhook
- `POST /webhook/order` → Update order status  

---

## 📌 Request and Response Examples

### Create Product
```http
POST /products
Content-Type: application/json

{
  "name": "T-Shirt",
  "price": 59.90,
  "variations": [
    { "name": "S", "quantity": 10 },
    { "name": "M", "quantity": 15 }
  ]
}
```
**Response**
```json
{
  "status": "success",
  "message": "Product created",
  "id": 1
}
```

---

### Apply Coupon
```http
POST /orders/coupon
Content-Type: application/json

{
  "coupon_code": "DISCOUNT10"
}
```
**Response**
```json
{
  "status": "success",
  "message": "Coupon applied successfully",
  "coupon": {
    "id": 1,
    "code": "DISCOUNT10",
    "value": 10.00,
    "minimum_value": 50.00
  }
}
```

---

### Checkout Order
```http
POST /orders/checkout
Content-Type: application/json

{
  "postal_code": "01001-000",
  "address": "Example Street, 123"
}
```
**Response**
```json
{
  "status": "success",
  "message": "Order placed successfully",
  "order_id": 10
}
```

---

## 📦 Deploy with Docker
```bash
docker-compose up -d --build
docker-compose logs -f
docker exec -it mini-erp-php bash
```

---

## 📌 Important Notes
- **CORS enabled** via `CorsHook.php`  
- **JSON responses** using `api_helper.php`  
- **Shipping rules:**
  - Subtotal > R$ 200 → Free shipping  
  - Subtotal between R$ 52 and R$ 166.59 → R$ 15.00  
  - Otherwise → R$ 20.00  
- **Coupons** have minimum value based on subtotal  
- **Webhook:**
  - `status = canceled` → Delete order  
  - Otherwise → Update status  

---

## 🤝 Contributions
This project is **open to suggestions, improvements, and corrections**.  
Feel free to:  
- **Open an issue**  
- **Send a pull request**  

Your feedback is very welcome! 🚀
