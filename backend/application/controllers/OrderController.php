<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OrderController (REST API)
 *
 * Handles cart operations, order creation, coupon application, and shipping calculation.
 * Returns responses in JSON format following REST API best practices.
 */
class OrderController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Order_model', 'Product_model', 'Coupon_model']);
        $this->load->helper(['url', 'api']); // Custom API helper
        $this->load->library(['session']);
    }

    /**
     * GET /orders/cart
     * Show current cart items.
     */
    public function cart()
    {
        $cart = $this->session->userdata('cart') ?? [];
        $subtotal = $this->calculateSubtotal();
        $shipping = $this->calculateShipping($subtotal);
        $total = $subtotal + $shipping;

        return respondSuccess($this->output, [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total
        ]);
    }

    /**
     * POST /orders/cart
     * Add product to cart.
     */
    public function add_to_cart()
    {
        $input = json_decode($this->input->raw_input_stream, true);
        $productId = (int) ($input['product_id'] ?? 0);
        $variationId = (int) ($input['variation_id'] ?? 0);
        $quantity = (int) ($input['quantity'] ?? 1);

        $product = $this->Product_model->getById($productId);
        if (!$product) {
            return respondError($this->output, 'Product not found', 404);
        }

        $cart = $this->session->userdata('cart') ?? [];
        $cart[] = [
            'product_id' => $productId,
            'variation_id' => $variationId,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        ];

        $this->session->set_userdata('cart', $cart);

        return respondSuccess($this->output, ['message' => 'Product added to cart', 'cart' => $cart], 201);
    }

    /**
     * DELETE /orders/cart/{index}
     * Remove item from cart by index.
     */
    public function remove_from_cart($index)
    {
        $cart = $this->session->userdata('cart') ?? [];

        if (!isset($cart[$index])) {
            return respondError($this->output, 'Item not found in cart', 404);
        }

        unset($cart[$index]);
        $this->session->set_userdata('cart', array_values($cart));

        return respondSuccess($this->output, ['message' => 'Item removed from cart', 'cart' => $cart]);
    }

    /**
     * POST /orders/coupon
     * Apply coupon to cart.
     */
    public function apply_coupon()
    {
        $input = json_decode($this->input->raw_input_stream, true);
        $code = trim($input['coupon_code'] ?? '');

        $coupon = $this->Coupon_model->getByCode($code);
        if (!$coupon) {
            return respondError($this->output, 'Invalid coupon code', 400);
        }

        $subtotal = $this->calculateSubtotal();
        if ($subtotal < $coupon['minimum_value']) {
            return respondError($this->output, 'Subtotal does not meet coupon requirements', 400);
        }

        $this->session->set_userdata('coupon', $coupon);

        return respondSuccess($this->output, ['message' => 'Coupon applied successfully', 'coupon' => $coupon]);
    }

    /**
     * POST /orders/checkout
     * Checkout and create order.
     */
    public function checkout()
    {
        $input = json_decode($this->input->raw_input_stream, true);
        $cart = $this->session->userdata('cart') ?? [];

        if (empty($cart)) {
            return respondError($this->output, 'Cart is empty', 400);
        }

        if (empty($input['postal_code']) || empty($input['address'])) {
            return respondError($this->output, 'Please provide valid address details', 400);
        }

        $subtotal = $this->calculateSubtotal();
        $shipping = $this->calculateShipping($subtotal);
        $coupon = $this->session->userdata('coupon');
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

        $orderId = $this->Order_model->create($orderData, $cart);

        $this->session->unset_userdata(['cart', 'coupon']);

        return respondSuccess($this->output, [
            'message' => 'Order placed successfully',
            'order_id' => $orderId
        ], 201);
    }

    /**
     * GET /orders/{id}
     * Show order details by ID.
     */
    public function details($id)
    {
        $order = $this->Order_model->getById($id);
        if (!$order) {
            return respondError($this->output, 'Order not found', 404);
        }

        return respondSuccess($this->output, ['order' => $order]);
    }

    /**
     * Calculate cart subtotal.
     */
    private function calculateSubtotal(): float
    {
        $cart = $this->session->userdata('cart') ?? [];
        return array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
    }

    /**
     * Calculate shipping cost based on subtotal.
     */
    private function calculateShipping(float $subtotal): float
    {
        if ($subtotal > 200) {
            return 0.00;
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00;
        }
        return 20.00;
    }
}
