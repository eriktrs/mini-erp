<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
    protected $table = 'product';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all products
     *
     * @return array
     */
    public function getAll()
    {
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get a single product by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    /**
     * Create a new product
     *
     * @param array $data
     * @return int|bool Inserted ID or false on failure
     */
    public function create($data)
    {
        $inserted = $this->db->insert($this->table, $data);
        return $inserted ? $this->db->insert_id() : false;
    }

    /**
     * Update a product by ID
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete a product by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}
