<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Webhook_model
 * 
 * Handles webhook requests to update or delete orders based on status.
 */
class Webhook_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Process webhook data to update or delete order status.
     * 
     * @param int $orderId The ID of the order.
     * @param string $status The new status of the order.
     * @return bool True on success, false on failure.
     */
    public function processOrderStatus(int $orderId, string $status): bool
    {
        if (strtolower($status) === 'cancelled' || strtolower($status) === 'canceled') {
            // Delete the order if status is cancelled
            return $this->deleteOrder($orderId);
        } else {
            // Update the order status otherwise
            return $this->updateOrderStatus($orderId, $status);
        }
    }

    /**
     * Delete an order by ID.
     * 
     * @param int $orderId
     * @return bool
     */
    private function deleteOrder(int $orderId): bool
    {
        // Start transaction to ensure data integrity
        $this->db->trans_start();

        // Delete related order items first (if any)
        $this->db->delete('order_item', ['order_id' => $orderId]);

        // Delete the order
        $this->db->delete('orders', ['id' => $orderId]);

        // Complete the transaction
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Update the status of an order.
     * 
     * @param int $orderId
     * @param string $status
     * @return bool
     */
    private function updateOrderStatus(int $orderId, string $status): bool
    {
        $this->db->where('id', $orderId);
        return $this->db->update('orders', ['status' => $status]);
    }
}
