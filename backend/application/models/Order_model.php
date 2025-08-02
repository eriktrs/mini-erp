<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_model extends CI_Model
{
    protected $table = 'orders';
    protected $itemTable = 'order_item';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all orders with their items (including product and variation names).
     *
     * @return array
     */
    public function get_all()
    {
        // Get all orders
        $orders = $this->db->get($this->table)->result_array();

        foreach ($orders as &$order) {
            $order['items'] = $this->get_order_items($order['id']);
        }

        return $orders;
    }

    /**
     * Get specific order by ID with items.
     *
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        $order = $this->db
            ->get_where($this->table, ['id' => $id])
            ->row_array();

        if ($order) {
            $order['items'] = $this->get_order_items($id);
        }

        return $order;
    }

    /**
     * Fetch items for an order with product and variation names.
     *
     * @param int $orderId
     * @return array
     */
    private function get_order_items($orderId)
    {
        return $this->db
            ->select('ordemitem.*, p.name AS product_name, pv.name AS variation_name')
            ->from($this->itemTable . ' AS ordemitem')
            ->join('product AS p', 'ordemitem.product_id = p.id', 'left')
            ->join('product_variation AS pv', 'ordemitem.variation_id = pv.id', 'left')
            ->where('ordemitem.order_id', $orderId)
            ->get()
            ->result_array();
    }

    /**
     * Get orders with optional filters, pagination, and sorting.
     *
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_filtered($filters = [], $limit = 10, $offset = 0)
    {
        $this->db->from($this->table);

        if (isset($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $orders = $this->db->get()->result_array();

        foreach ($orders as &$order) {
            $order['subtotal'] = (float) $order['subtotal'];
            $order['shipping'] = (float) $order['shipping'];
            $order['total'] = (float) $order['total'];
            $order['items'] = $this->get_order_items($order['id']);
        }

        return $orders;
    }

    /**
     * Count total orders with optional filters.
     *
     * @param array $filters
     * @return int
     */
    public function count_filtered($filters = [])
    {
        if (isset($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        return $this->db->count_all_results($this->table);
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
     * Insert an order item.
     *
     * @param int $orderId
     * @param array $itemData
     * @return int Inserted item ID
     */
    public function insertItem($orderId, $itemData)
    {
        $itemData['order_id'] = $orderId;
        $this->db->insert($this->itemTable, $itemData);
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
