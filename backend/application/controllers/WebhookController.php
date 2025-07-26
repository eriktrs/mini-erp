<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WebhookController (REST API)
 *
 * Handles incoming webhook requests to update or delete orders based on status.
 */
class WebhookController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Order_model');
        $this->load->helper(['api']); // Custom API response helper
    }

    /**
     * POST /webhook/order-status
     * Receive webhook to update or cancel an order.
     * Expected JSON:
     * {
     *   "order_id": 123,
     *   "status": "cancelled" | "shipped" | "pending"
     * }
     */
    public function update_order_status()
    {
        // Validate HTTP method
        if ($this->input->method() !== 'post') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        // Parse JSON payload
        $payload = json_decode($this->input->raw_input_stream, true);

        if (!$payload || !isset($payload['order_id']) || !isset($payload['status'])) {
            return respondError($this->output, 'Missing required fields: order_id or status', 400);
        }

        $orderId = (int)$payload['order_id'];
        $status = strtolower(trim($payload['status']));

        // Validate order existence
        $order = $this->Order_model->getById($orderId);
        if (!$order) {
            return respondError($this->output, 'Order not found', 404);
        }

        // If status is "cancelled", delete the order
        if ($status === 'cancelled') {
            $deleted = $this->Order_model->delete($orderId);
            if ($deleted) {
                return respondSuccess($this->output, ['message' => 'Order cancelled and deleted successfully']);
            }
            return respondError($this->output, 'Failed to delete order', 500);
        }

        // Otherwise, update order status
        $updated = $this->Order_model->updateStatus($orderId, $status);
        if ($updated) {
            return respondSuccess($this->output, ['message' => 'Order status updated successfully']);
        }

        return respondError($this->output, 'Failed to update order status', 500);
    }
}
