<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrderController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Order_model', 'Cart_model', 'Product_model', 'ProductStock_model', 'Coupon_model']);
        $this->load->helper(['url', 'api']);
    }

    /**
     * GET /orders
     * List all orders.
     */

    public function index()
    {
        $status = $this->input->get('status');
        $page = (int) $this->input->get('page') ?: 1;
        $limit = (int) $this->input->get('limit') ?: 5;
        $offset = ($page - 1) * $limit;

        $filters = [];
        if (!empty($status)) {
            $filters['status'] = $status;
        }

        $orders = $this->Order_model->get_filtered($filters, $limit, $offset);
        $totalOrders = $this->Order_model->count_filtered($filters);

        return respondSuccess($this->output, [
            'orders' => $orders,
            'pagination' => [
                'current_page' => $page,
                'limit' => $limit,
                'total_orders' => $totalOrders,
                'total_pages' => ceil($totalOrders / $limit)
            ]
        ]);
    }

    /**
     * GET /orders/cart
     * Show current cart items.
     */
    public function cart()
    {
        $cart = $this->Cart_model->getAll();
        $subtotal = $this->Cart_model->calculateSubtotal();
        $shipping = $this->calculateShipping($subtotal);
        $coupon = $this->Cart_model->getAppliedCoupon();
        $discount = $coupon ? $coupon['value'] : 0;
        $total = max(0, $subtotal + $shipping - $discount);

        return respondSuccess($this->output, [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'coupon' => $coupon,
            'discount' => $discount,
            'total' => $total
        ]);
    }

    /**
     * POST /orders/cart
     * Add product to cart.
     */
    public function addToCart()
    {
        $input = json_decode($this->input->raw_input_stream, true);
        if (!isset($input['product_id'], $input['variation_id'], $input['qty'])) {
            return respondError($this->output, 'Missing required fields', 400);
        }

        $product = $this->Product_model->getById($input['product_id']);
        if (!$product) {
            return respondError($this->output, 'Product not found', 404);
        }

        $variationStock = $this->ProductStock_model->get_by_variation($input['variation_id']);
        if (!$variationStock || $variationStock['quantity'] < $input['qty']) {
            return respondError($this->output, 'Insufficient stock', 400);
        }

        $itemData = [
            'product_id' => $input['product_id'],
            'variation_id' => $input['variation_id'],
            'price' => $product['price'],
            'quantity' => $input['qty']
        ];

        $id = $this->Cart_model->addItem($itemData);

        return respondSuccess($this->output, ['message' => 'Product added to cart', 'id' => $id], 201);
    }

    /**
     * DELETE /orders/cart/{id}
     * Remove cart item by ID.
     */
    public function removeFromCart($id)
    {
        $deleted = $this->Cart_model->removeItem($id);
        if (!$deleted) {
            return respondError($this->output, 'Item not found', 404);
        }
        return respondSuccess($this->output, ['message' => 'Item removed']);
    }

    /**
     * PUT /orders/cart/{id}
     * Update quantity for cart item.
     */
    public function updateQuantity($id)
    {
        $input = json_decode($this->input->raw_input_stream, true);
        if (!isset($input['qty'])) {
            return respondError($this->output, 'Missing quantity', 400);
        }
        $updated = $this->Cart_model->updateQuantity($id, $input['qty']);
        if (!$updated) {
            return respondError($this->output, 'Item not found', 404);
        }
        return respondSuccess($this->output, ['message' => 'Quantity updated']);
    }

    /**
     * POST /orders/coupon
     * Apply coupon to cart.
     */
    public function applyCoupon()
    {
        $input = json_decode($this->input->raw_input_stream, true);
        $code = trim($input['coupon_code'] ?? '');

        $coupon = $this->Coupon_model->getByCode($code);
        if (!$coupon) {
            return respondError($this->output, 'Invalid coupon code', 400);
        }

        $subtotal = $this->Cart_model->calculateSubtotal();
        if ($subtotal < $coupon['minimum_value']) {
            return respondError($this->output, 'Subtotal does not meet coupon requirements', 400);
        }

        $this->Cart_model->applyCoupon($coupon['id']);

        return respondSuccess($this->output, ['message' => 'Coupon applied successfully', 'coupon' => $coupon]);
    }

    /**
     * POST /orders/checkout
     * Checkout and create order.
     */
    public function checkout()
    {
        $input = json_decode($this->input->raw_input_stream, true);
        if (empty($input['postal_code']) || empty($input['address'])) {
            return respondError($this->output, 'Please provide valid address details', 400);
        }

        $cart = $this->Cart_model->getAll();

        if (empty($cart)) {
            return respondError($this->output, 'Cart is empty', 400);
        }

        $subtotal = $this->Cart_model->calculateSubtotal();
        $shipping = $this->calculateShipping($subtotal);
        $coupon = $this->Cart_model->getAppliedCoupon();
        $discount = $coupon ? $coupon['value'] : 0;
        $total = max(0, $subtotal + $shipping - $discount);

        $orderData = [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'postal_code' => $input['postal_code'],
            'address' => $input['address'],
            'status' => 'pending'
        ];

        $orderId = $this->Order_model->insert($orderData);

        foreach ($cart as $item) {

            $itemData = [
                'product_id' => $item['product_id'],
                'variation_id' => $item['variation_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];

            $this->Order_model->insertItem($orderId, $itemData);

            $variationStock = $this->ProductStock_model->get_by_variation($item['variation_id']);
            $newQty = max(0, $variationStock['quantity'] - $item['quantity']);

            // Update stock quantity
            $this->ProductStock_model->decrease_stock($item['variation_id'], $newQty);
        }

        $this->Cart_model->clearCart();

        return respondSuccess($this->output, ['message' => 'Order placed successfully', 'order_id' => $orderId], 201);
    }

    private function calculateShipping(float $subtotal): float
    {
        if ($subtotal > 200) return 0.00;
        if ($subtotal >= 52 && $subtotal <= 166.59) return 15.00;
        return 20.00;
    }
}
