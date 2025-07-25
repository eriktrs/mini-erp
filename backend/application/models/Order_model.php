<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model
{
    protected $table = 'orders';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all orders from the database.
     *
     * @return array
     */
    public function get_all()
    {
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get a specific order by ID.
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
     * Insert a new order.
     *
     * @param array $data
     * @return int Inserted order ID
     */
    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update an existing order by ID.
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
     * Delete an order by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Update order status based on webhook.
     * If status is "cancelled", delete the order.
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function handle_webhook($id, $status)
    {
        if (strtolower($status) === 'cancelled') {
            return $this->delete($id);
        }

        return $this->update($id, ['status' => $status]);
    }
}
