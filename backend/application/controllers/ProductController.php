<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProductController (REST API)
 *
 * Provides CRUD operations for products in JSON format.
 */
class ProductController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->helper(['url', 'api']); // Custom API helper for standardized responses
    }

    /**
     * GET /products
     * Fetch all products.
     */
    public function index()
    {
        if ($this->input->method() !== 'get') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        try {
            $products = $this->Product_model->getAll();
            return respondSuccess($this->output, ['products' => $products]);
        } catch (Exception $e) {
            return respondError($this->output, 'Internal Server Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /products/{id}
     * Fetch a single product by ID.
     */
    public function show($id)
    {
        if ($this->input->method() !== 'get') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $product = $this->Product_model->getById((int)$id);
        if ($product) {
            return respondSuccess($this->output, ['product' => $product]);
        }
        return respondError($this->output, 'Product not found', 404);
    }

    /**
     * POST /products
     * Create a new product.
     */
    public function store()
    {
        if ($this->input->method() !== 'post') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $input = json_decode($this->input->raw_input_stream, true);
        if (!isset($input['name'], $input['price'])) {
            return respondError($this->output, 'Missing required fields: name, price', 400);
        }

        $data = [
            'name' => htmlspecialchars(trim($input['name'])),
            'price' => (float)$input['price']
        ];

        $id = $this->Product_model->create($data);
        return respondSuccess($this->output, ['message' => 'Product created', 'id' => $id], 201);
    }

    /**
     * PUT /products/{id}
     * Update product by ID.
     */
    public function update($id)
    {
        if ($this->input->method() !== 'put') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $input = json_decode($this->input->raw_input_stream, true);
        if (!isset($input['name'], $input['price'])) {
            return respondError($this->output, 'Missing required fields: name, price', 400);
        }

        $data = [
            'name' => htmlspecialchars(trim($input['name'])),
            'price' => (float)$input['price']
        ];

        $updated = $this->Product_model->update((int)$id, $data);
        if ($updated) {
            return respondSuccess($this->output, ['message' => 'Product updated']);
        }
        return respondError($this->output, 'Product not found', 404);
    }

    /**
     * DELETE /products/{id}
     * Delete product by ID.
     */
    public function delete($id)
    {
        if ($this->input->method() !== 'delete') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $deleted = $this->Product_model->delete((int)$id);
        if ($deleted) {
            return respondSuccess($this->output, ['message' => 'Product deleted']);
        }
        return respondError($this->output, 'Product not found', 404);
    }
}
