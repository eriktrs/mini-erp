# Mini ERP - Pedidos, Produtos, Cupons e Estoque

[![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](./README.md)
[![en](https://img.shields.io/badge/lang-en-red.svg)](./README.en.md)

---

## 📌 Visão Geral
Este projeto é um **Mini ERP** desenvolvido em **PHP (CodeIgniter 3)** para gerenciamento de **Produtos, Pedidos, Estoque e Cupons**, com **frontend em Vue.js** e **containerização com Docker**.  
O sistema oferece:

✅ Cadastro e gerenciamento de produtos com variações e estoque  
✅ Carrinho de compras com cálculo automático de frete  
✅ Aplicação de cupons de desconto com regras de valor mínimo  
✅ Finalização de pedidos com cálculo de subtotal, frete e descontos  
✅ Consulta de endereço via **ViaCEP API**  
✅ Webhook para atualização/cancelamento de pedidos  
✅ **API RESTful** com respostas em **JSON** e suporte a **CORS**  

---

## 🛠 Tecnologias Utilizadas
- **Backend:** PHP 7.4, CodeIgniter 3  
- **Frontend:** Vue 3 (Vite + TypeScript)  
- **Banco de Dados:** MySQL  
- **Estilização:** TailwindCSS  
- **Integração:** Axios, ViaCEP API  
- **Containerização:** Docker + Docker Compose  

---

## 📂 Estrutura do Projeto
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

## ⚙️ Configuração do Ambiente
1. Crie um arquivo **`.env`** na raiz do projeto com base no **`.env.example`**  
2. Crie um arquivo **`.env`** dentro da pasta `frontend/` com base no **`.env.example`**  
3. URLs do projeto:
   - **Backend (API):** `http://localhost:8080`
   - **Frontend (Vue):** `http://localhost:5173`

---

## 🗄 Banco de Dados
Tabelas principais:
- `products`
- `product_variations`
- `product_stock`
- `coupons`
- `orders`
- `order_items`

Arquivo SQL de inicialização:  
`docker/init/init_db.sql`

---

## 🚀 Como Executar o Projeto
### Pré-requisitos
- WSL / Linux  
- Docker e Docker Compose  
- Node.js  

### Instalação
```bash
# Clonar o repositório
git clone https://github.com/eriktrs/mini-erp.git
cd mini-erp

# Subir containers
docker-compose up -d --build
```

---

## 📡 Endpoints da API

### Produtos
- `GET /products` → Lista todos os produtos  
- `POST /products` → Cria um novo produto  

### Carrinho
- `GET /orders/cart` → Lista itens do carrinho  
- `POST /orders/cart` → Adiciona item ao carrinho  
- `PUT /orders/cart/{id}` → Atualiza quantidade  
- `DELETE /orders/cart/{id}` → Remove item  

### Cupons
- `POST /orders/coupon` → Aplica cupom de desconto  

### Pedidos
- `POST /orders/checkout` → Finaliza pedido  

### Webhook
- `POST /webhook/order` → Atualiza status do pedido  

---

## 📌 Exemplos de Requisição e Resposta

### Criar Produto
```http
POST /products
Content-Type: application/json

{
  "name": "Camiseta",
  "price": 59.90,
  "variations": [
    { "name": "P", "quantity": 10 },
    { "name": "M", "quantity": 15 }
  ]
}
```
**Resposta**
```json
{
  "status": "success",
  "message": "Product created",
  "id": 1
}
```

---

### Aplicar Cupom
```http
POST /orders/coupon
Content-Type: application/json

{
  "coupon_code": "DESCONTO10"
}
```
**Resposta**
```json
{
  "status": "success",
  "message": "Coupon applied successfully",
  "coupon": {
    "id": 1,
    "code": "DESCONTO10",
    "value": 10.00,
    "minimum_value": 50.00
  }
}
```

---

### Finalizar Pedido
```http
POST /orders/checkout
Content-Type: application/json

{
  "postal_code": "01001-000",
  "address": "Rua Exemplo, 123"
}
```
**Resposta**
```json
{
  "status": "success",
  "message": "Order placed successfully",
  "order_id": 10
}
```

---

## 📦 Deploy com Docker
```bash
docker-compose up -d --build
docker-compose logs -f
docker exec -it mini-erp-php bash
```

---

## 📌 Observações Importantes
- **CORS habilitado** via `CorsHook.php`  
- **Respostas em JSON** usando `api_helper.php`  
- **Regras de Frete:**
  - Subtotal > R$ 200 → Frete grátis  
  - Subtotal entre R$ 52 e R$ 166,59 → R$ 15,00  
  - Caso contrário → R$ 20,00  
- **Cupons** possuem valor mínimo baseado no subtotal  
- **Webhook:**
  - `status = canceled` → Remove pedido  
  - Caso contrário → Atualiza status  

---

## 🤝 Contribuições
Este projeto está **aberto a sugestões, melhorias e correções**.  
Caso tenha alguma ideia ou ajuste, sinta-se à vontade para:  
- **Abrir uma issue**  
- **Enviar um pull request**  

Seu feedback será muito bem-vindo! 🚀
