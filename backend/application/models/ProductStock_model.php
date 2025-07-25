<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductStock_model extends CI_Model
{
    protected $table = 'product_stock';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all stock entries for a given product variation.
     *
     * @param int $variation_id
     * @return array
     */
    public function get_by_variation($variation_id)
    {
        return $this->db
            ->where('product_variation_id', $variation_id)
            ->get($this->table)
            ->result_array();
    }

    /**
     * Get a specific stock entry by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function get_by_id($id)
    {
        return $this->db
            ->get_where($this->table, ['id' => $id])
            ->row_array();
    }

    /**
     * Insert a new stock record.
     *
     * @param array $data
     * @return int Inserted stock ID
     */
    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update stock entry.
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
     * Delete a stock entry by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Decrease stock quantity.
     *
     * @param int $variation_id
     * @param int $quantity
     * @return bool
     */
    public function decrease_stock($variation_id, $quantity)
    {
        $this->db->where('product_variation_id', $variation_id);
        $this->db->set('quantity', 'quantity - ' . (int) $quantity, false);
        return $this->db->update($this->table);
    }

    /**
     * Increase stock quantity.
     *
     * @param int $variation_id
     * @param int $quantity
     * @return bool
     */
    public function increase_stock($variation_id, $quantity)
    {
        $this->db->where('product_variation_id', $variation_id);
        $this->db->set('quantity', 'quantity + ' . (int) $quantity, false);
        return $this->db->update($this->table);
    }
}
