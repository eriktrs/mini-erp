<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderItem_model extends CI_Model
{
    protected $table = 'order_item';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all order items from the database.
     *
     * @return array
     */
    public function get_all()
    {
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get items by order ID.
     *
     * @param int $order_id
     * @return array
     */
    public function get_by_order($order_id)
    {
        return $this->db
            ->get_where($this->table, ['order_id' => $order_id])
            ->result_array();
    }

    /**
     * Insert a new item into the order.
     *
     * @param array $data
     * @return int Inserted item ID
     */
    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Insert multiple items in a batch.
     *
     * @param array $items
     * @return bool
     */
    public function insert_batch($items)
    {
        return $this->db->insert_batch($this->table, $items);
    }

    /**
     * Delete items by order ID.
     *
     * @param int $order_id
     * @return bool
     */
    public function delete_by_order($order_id)
    {
        return $this->db->delete($this->table, ['order_id' => $order_id]);
    }

    /**
     * Delete a specific order item by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}
