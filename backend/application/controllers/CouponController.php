<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CouponController (REST API)
 *
 * Provides CRUD operations for coupons in JSON format.
 */
class CouponController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Coupon_model');
        $this->load->helper(['url', 'api']); // Use custom API helper
    }

    /**
     * GET /coupons
     * Retrieve all available coupons.
     */
    public function index()
    {
        if ($this->input->method() !== 'get') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $coupons = $this->Coupon_model->getAll();
        return respondSuccess($this->output, ['coupons' => $coupons]);
    }

    /**
     * GET /coupons/{id}
     * Retrieve a specific coupon by ID.
     */
    public function show($id)
    {
        if ($this->input->method() !== 'get') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $coupon = $this->Coupon_model->getById((int)$id);
        if ($coupon) {
            return respondSuccess($this->output, ['coupon' => $coupon]);
        }
        return respondError($this->output, 'Coupon not found', 404);
    }

    /**
     * POST /coupons
     * Create a new coupon.
     * Expected JSON:
     * {
     *   "code": "DISCOUNT10",
     *   "value": 10.00,
     *   "valid_until": "2025-12-31",
     *   "minimum_value": 50.00
     * }
     */
    public function store()
    {
        if ($this->input->method() !== 'post') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $input = json_decode($this->input->raw_input_stream, true);
        if (!$this->validateCoupon($input)) {
            return respondError($this->output, 'Invalid coupon data', 400);
        }

        $data = [
            'code'          => htmlspecialchars(trim($input['code'])),
            'value'         => (float)$input['value'],
            'valid_until'   => $input['valid_until'],
            'minimum_value' => (float)$input['minimum_value']
        ];

        $couponId = $this->Coupon_model->create($data);
        if ($couponId) {
            return respondSuccess($this->output, [
                'message' => 'Coupon created successfully',
                'id' => $couponId
            ], 201);
        }

        return respondError($this->output, 'Failed to create coupon', 500);
    }

    /**
     * PUT /coupons/{id}
     * Update an existing coupon.
     */
    public function update($id)
    {
        if ($this->input->method() !== 'put') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $input = json_decode($this->input->raw_input_stream, true);
        if (!$this->validateCoupon($input)) {
            return respondError($this->output, 'Invalid coupon data', 400);
        }

        $data = [
            'code'          => htmlspecialchars(trim($input['code'])),
            'value'         => (float)$input['value'],
            'valid_until'   => $input['valid_until'],
            'minimum_value' => (float)$input['minimum_value']
        ];

        $updated = $this->Coupon_model->update((int)$id, $data);
        if ($updated) {
            return respondSuccess($this->output, ['message' => 'Coupon updated successfully']);
        }

        return respondError($this->output, 'Coupon not found or update failed', 404);
    }

    /**
     * DELETE /coupons/{id}
     * Delete a coupon.
     */
    public function delete($id)
    {
        if ($this->input->method() !== 'delete') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $deleted = $this->Coupon_model->delete((int)$id);
        if ($deleted) {
            return respondSuccess($this->output, ['message' => 'Coupon deleted successfully']);
        }
        return respondError($this->output, 'Coupon not found or delete failed', 404);
    }

    /**
     * Validate coupon payload.
     */
    private function validateCoupon($data): bool
    {
        return isset($data['code'], $data['value'], $data['valid_until'], $data['minimum_value']) &&
               !empty($data['code']) &&
               is_numeric($data['value']) &&
               strtotime($data['valid_until']) !== false &&
               is_numeric($data['minimum_value']);
    }
}
