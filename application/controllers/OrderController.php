<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Order Controller
 *
 * Handles cart operations, order creation, coupon application, and shipping calculation.
 */
class OrderController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Order_model', 'Product_model', 'Coupon_model']);
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation']);
    }

    /**
     * Show current cart items.
     */
    public function cart()
    {
        $data['cart'] = $this->session->userdata('cart') ?? [];
        $data['subtotal'] = $this->calculateSubtotal();
        $data['shipping'] = $this->calculateShipping($data['subtotal']);
        $data['total'] = $data['subtotal'] + $data['shipping'];

        $this->load->view('orders/cart', $data);
    }

    /**
     * Add product to cart.
     */
    public function add_to_cart()
    {
        $productId = (int) $this->input->post('product_id');
        $variationId = (int) $this->input->post('variation_id');
        $quantity = (int) $this->input->post('quantity');

        $product = $this->Product_model->getById($productId);
        if (!$product) {
            $this->session->set_flashdata('error', 'Product not found.');
            redirect('orders/cart');
            return;
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
        $this->session->set_flashdata('success', 'Product added to cart.');
        redirect('orders/cart');
    }

    /**
     * Remove item from cart.
     */
    public function remove_from_cart($index)
    {
        $cart = $this->session->userdata('cart') ?? [];
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $this->session->set_userdata('cart', array_values($cart));
            $this->session->set_flashdata('success', 'Item removed from cart.');
        }
        redirect('orders/cart');
    }

    /**
     * Apply coupon to cart.
     */
    public function apply_coupon()
    {
        $code = trim($this->input->post('coupon_code'));
        $coupon = $this->Coupon_model->getByCode($code);

        if (!$coupon) {
            $this->session->set_flashdata('error', 'Invalid coupon code.');
            redirect('orders/cart');
            return;
        }

        $subtotal = $this->calculateSubtotal();

        if ($subtotal < $coupon['minimum_value']) {
            $this->session->set_flashdata('error', 'Subtotal does not meet coupon requirements.');
            redirect('orders/cart');
            return;
        }

        $this->session->set_userdata('coupon', $coupon);
        $this->session->set_flashdata('success', 'Coupon applied successfully.');
        redirect('orders/cart');
    }

    /**
     * Checkout and create order.
     */
    public function checkout()
    {
        $cart = $this->session->userdata('cart') ?? [];
        if (empty($cart)) {
            $this->session->set_flashdata('error', 'Cart is empty.');
            redirect('orders/cart');
            return;
        }

        $this->form_validation->set_rules('postal_code', 'Postal Code', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', 'Please provide valid address details.');
            redirect('orders/cart');
            return;
        }

        $subtotal = $this->calculateSubtotal();
        $shipping = $this->calculateShipping($subtotal);
        $coupon = $this->session->userdata('coupon');
        $discount = $coupon ? $coupon['value'] : 0;

        $total = max(0, $subtotal + $shipping - $discount);

        // Prepare order data
        $orderData = [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'postal_code' => $this->input->post('postal_code'),
            'address' => $this->input->post('address'),
            'status' => 'pending'
        ];

        $orderId = $this->Order_model->create($orderData, $cart);

        // Clear cart and coupon
        $this->session->unset_userdata(['cart', 'coupon']);

        $this->session->set_flashdata('success', 'Order placed successfully.');
        redirect('orders/details/' . $orderId);
    }

    /**
     * Show order details.
     */
    public function details($id)
    {
        $order = $this->Order_model->getById($id);
        if (!$order) {
            $this->session->set_flashdata('error', 'Order not found.');
            redirect('orders/cart');
            return;
        }

        $data['order'] = $order;
        $this->load->view('orders/details', $data);
    }

    /**
     * Calculate cart subtotal.
     */
    private function calculateSubtotal(): float
    {
        $cart = $this->session->userdata('cart') ?? [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return $subtotal;
    }

    /**
     * Calculate shipping cost based on subtotal.
     */
    private function calculateShipping(float $subtotal): float
    {
        if ($subtotal > 200) {
            return 0.00; // Free shipping
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00;
        }
        return 20.00;
    }
}
