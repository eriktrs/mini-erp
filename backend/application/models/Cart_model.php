<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart_model extends CI_Model
{
    protected $table = 'cart';
    protected $couponTable = 'cart_coupon';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all items in the cart with product details.
     * Includes product name and variation name.
     */
    public function getAll()
    {
        return $this->db->select('cart.id, cart.product_id, cart.variation_id, cart.price, cart.quantity, product.name AS product_name, product_variation.name AS variation_name')
            ->from($this->table)
            ->join('product', 'product.id = cart.product_id', 'left')
            ->join('product_variation', 'product_variation.id = cart.variation_id', 'left')
            ->get()
            ->result_array();
    }

    /**
     * Add an item to the cart.
     */
    public function addItem($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Remove a specific item from the cart by ID.
     */
    public function removeItem($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Update quantity for a specific cart item.
     */
    public function updateQuantity($id, $qty)
    {
        return $this->db->update($this->table, ['quantity' => $qty], ['id' => $id]);
    }

    /**
     * Clear the entire cart and applied coupon.
     */
    public function clearCart()
    {
        $this->db->empty_table($this->table);
        $this->db->empty_table($this->couponTable);
    }

    /**
     * Calculate subtotal of all items in the cart.
     */
    public function calculateSubtotal()
    {
        $cart = $this->getAll();
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }

    /**
     * Apply a coupon (only one allowed).
     */
    public function applyCoupon($couponId)
    {
        $this->db->empty_table($this->couponTable);
        $this->db->insert($this->couponTable, ['coupon_id' => $couponId]);
    }

    /**
     * Get applied coupon details.
     */
    public function getAppliedCoupon()
    {
        $query = $this->db->select('coupon.*')
            ->from($this->couponTable)
            ->join('coupon', 'coupon.id = cart_coupon.coupon_id')
            ->get();

        return $query->row_array();
    }
}
