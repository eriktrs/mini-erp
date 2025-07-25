<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Coupon Controller
 *
 * Manages coupon CRUD operations (Create, Read, Update, Delete).
 * This controller serves as an API endpoint, returning JSON responses.
 */
class CouponController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Coupon_model');
        $this->output->set_content_type('application/json'); // Force JSON output
    }

    /**
     * Retrieve all available coupons.
     * GET /coupon
     */
    public function index()
    {
        $coupons = $this->Coupon_model->getAll();
        return $this->respond(['data' => $coupons], 200);
    }

    /**
     * Retrieve a specific coupon by ID.
     * GET /coupon/{id}
     */
    public function show($id)
    {
        $coupon = $this->Coupon_model->getById((int) $id);
        if ($coupon) {
            return $this->respond(['data' => $coupon], 200);
        }
        return $this->respond(['error' => 'Coupon not found'], 404);
    }

    /**
     * Create a new coupon.
     * POST /coupon
     * Expected JSON: { "code": "DISCOUNT10", "value": 10.00, "valid_until": "2025-12-31", "minimum_value": 50.00 }
     */
    public function store()
    {
        $payload = json_decode(trim(file_get_contents('php://input')), true);

        if (!$this->validateCoupon($payload)) {
            return $this->respond(['error' => 'Invalid coupon data'], 400);
        }

        $couponId = $this->Coupon_model->create($payload);
        if ($couponId) {
            return $this->respond(['message' => 'Coupon created successfully', 'id' => $couponId], 201);
        }
        return $this->respond(['error' => 'Failed to create coupon'], 500);
    }

    /**
     * Update an existing coupon.
     * PUT /coupon/{id}
     * Expected JSON: { "code": "NEWCODE", "value": 15.00, "valid_until": "2025-12-31", "minimum_value": 100.00 }
     */
    public function update($id)
    {
        $payload = json_decode(trim(file_get_contents('php://input')), true);

        if (!$this->validateCoupon($payload)) {
            return $this->respond(['error' => 'Invalid coupon data'], 400);
        }

        $updated = $this->Coupon_model->update((int) $id, $payload);
        if ($updated) {
            return $this->respond(['message' => 'Coupon updated successfully'], 200);
        }
        return $this->respond(['error' => 'Failed to update coupon'], 500);
    }

    /**
     * Delete a coupon.
     * DELETE /coupon/{id}
     */
    public function delete($id)
    {
        $deleted = $this->Coupon_model->delete((int) $id);
        if ($deleted) {
            return $this->respond(['message' => 'Coupon deleted successfully'], 200);
        }
        return $this->respond(['error' => 'Failed to delete coupon'], 500);
    }

    /**
     * Validate coupon payload.
     */
    private function validateCoupon($data)
    {
        return isset($data['code'], $data['value'], $data['valid_until'], $data['minimum_value']) &&
               !empty($data['code']) &&
               is_numeric($data['value']) &&
               strtotime($data['valid_until']) !== false &&
               is_numeric($data['minimum_value']);
    }

    /**
     * Helper method to send JSON response with HTTP status code.
     */
    private function respond(array $data, int $statusCode)
    {
        $this->output->set_status_header($statusCode);
        echo json_encode($data);
        exit;
    }
}
