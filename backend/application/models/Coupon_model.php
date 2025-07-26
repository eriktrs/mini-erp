<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coupon_model extends CI_Model
{
    protected $table = 'coupon';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all coupons.
     */
    public function getAll(): array
    {
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get coupon by ID.
     */
    public function getById(int $id): ?array
    {
        $query = $this->db->get_where($this->table, ['id' => $id]);
        return $query->row_array() ?: null;
    }

    /**
     * Get coupon by code.
     */
    public function getByCode(string $code): ?array
    {
        $query = $this->db->get_where($this->table, ['code' => $code]);
        return $query->row_array() ?: null;
    }

    /**
     * Check if coupon code exists.
     */
    public function existsByCode(string $code): bool
    {
        return $this->db->where('code', $code)->count_all_results($this->table) > 0;
    }

    /**
     * Create a new coupon.
     */
    public function create(array $data): int
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update coupon by ID.
     */
    public function update(int $id, array $data): bool
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete coupon by ID.
     */
    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Validate if coupon is applicable.
     */
    public function validateCoupon(string $code, float $subtotal): ?array
    {
        $coupon = $this->getByCode($code);

        if (!$coupon) {
            return null;
        }

        $today = date('Y-m-d');

        if ($coupon['valid_until'] < $today) {
            return null; // Expired
        }

        if ($subtotal < $coupon['minimum_value']) {
            return null; // Does not meet minimum requirement
        }

        return $coupon;
    }
}
