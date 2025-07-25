<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Webhook Controller
 *
 * Handles incoming webhook requests for updating or deleting orders based on status.
 * This controller is intended for API usage (JSON requests/responses).
 */
class WebhookController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Order_model');
        $this->output->set_content_type('application/json'); // Force JSON output
    }

    /**
     * Receive webhook POST request with order ID and status.
     * Expected JSON body: { "order_id": int, "status": string }
     */
    public function update_order_status()
    {
        // Read and decode JSON payload
        $payload = json_decode(trim(file_get_contents('php://input')), true);

        if (!$payload || !isset($payload['order_id']) || !isset($payload['status'])) {
            return $this->respond(['error' => 'Missing order_id or status'], 400);
        }

        $orderId = (int) $payload['order_id'];
        $status = strtolower(trim($payload['status']));

        // Validate order existence
        $order = $this->Order_model->getById($orderId);
        if (!$order) {
            return $this->respond(['error' => 'Order not found'], 404);
        }

        // If status is "cancelled", delete the order
        if ($status === 'cancelled') {
            $deleted = $this->Order_model->delete($orderId);
            if ($deleted) {
                return $this->respond(['message' => 'Order cancelled and removed successfully'], 200);
            }
            return $this->respond(['error' => 'Failed to delete order'], 500);
        }

        // Otherwise, update the status
        $updated = $this->Order_model->updateStatus($orderId, $status);
        if ($updated) {
            return $this->respond(['message' => 'Order status updated successfully'], 200);
        }

        return $this->respond(['error' => 'Failed to update order status'], 500);
    }

    /**
     * Helper method for sending JSON response with status code.
     */
    private function respond(array $data, int $statusCode)
    {
        $this->output->set_status_header($statusCode);
        echo json_encode($data);
        exit;
    }
}
