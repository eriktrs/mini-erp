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
     * Get all coupons from the database.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get coupon by code.
     *
     * @param string $code
     * @return array|null
     */
    public function getByCode($code)
    {
        return $this->db
            ->get_where($this->table, ['code' => $code])
            ->row_array();
    }

    /**
     * Insert a new coupon.
     *
     * @param array $data
     * @return int Inserted coupon ID
     */
    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update a coupon by ID.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete a coupon by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Check if the coupon is valid based on expiration date and minimum order value.
     *
     * @param string $code
     * @param float $subtotal
     * @return array|null
     */
    public function validate_coupon($code, $subtotal)
    {
        $coupon = $this->get_by_code($code);

        if (!$coupon) {
            return null;
        }

        $today = date('Y-m-d');

        // Check expiration date
        if (!empty($coupon['expires_at']) && $coupon['expires_at'] < $today) {
            return null;
        }

        // Check minimum order value
        if (!empty($coupon['minimum_value']) && $subtotal < $coupon['minimum_value']) {
            return null;
        }

        return $coupon;
    }
}
