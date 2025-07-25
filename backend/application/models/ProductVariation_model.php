<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductVariation_model extends CI_Model
{
    protected $table = 'product_variation';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all variations for a given product.
     *
     * @param int $product_id
     * @return array
     */
    public function get_by_product($product_id)
    {
        return $this->db
            ->where('product_id', $product_id)
            ->get($this->table)
            ->result_array();
    }

    /**
     * Get a specific variation by ID.
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
     * Insert a new variation.
     *
     * @param array $data
     * @return int Inserted variation ID
     */
    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update variation data.
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
     * Delete a variation by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}
