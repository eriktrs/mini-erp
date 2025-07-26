<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Product Controller (API REST)
 */
class ProductController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load models
        $this->load->model('Product_model');
        $this->load->helper('url');
    }

    /**
     * GET /products
     * List all products as JSON.
     */
    public function index()
    {
        $products = $this->Product_model->getAll();
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(['status' => 'success', 'data' => $products]));
    }

    /**
     * GET /products/{id}
     * Show single product by ID.
     */
    public function show($id)
    {
        $product = $this->Product_model->getById($id);

        if ($product) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['status' => 'success', 'data' => $product]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Product not found']));
        }
    }

    /**
     * POST /products
     * Create new product.
     */
    public function store()
    {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!isset($input['name']) || !isset($input['price'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid input']));
        }

        $data = [
            'name'  => $input['name'],
            'price' => $input['price'],
        ];

        $id = $this->Product_model->create($data);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(201)
            ->set_output(json_encode(['status' => 'success', 'message' => 'Product created', 'id' => $id]));
    }

    /**
     * PUT /products/{id}
     * Update product by ID.
     */
    public function update($id)
    {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!isset($input['name']) || !isset($input['price'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid input']));
        }

        $data = [
            'name'  => $input['name'],
            'price' => $input['price'],
        ];

        $updated = $this->Product_model->update($id, $data);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($updated ? 200 : 404)
            ->set_output(json_encode([
                'status' => $updated ? 'success' : 'error',
                'message' => $updated ? 'Product updated' : 'Product not found'
            ]));
    }

    /**
     * DELETE /products/{id}
     * Delete product by ID.
     */
    public function delete($id)
    {
        $deleted = $this->Product_model->delete($id);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($deleted ? 200 : 404)
            ->set_output(json_encode([
                'status' => $deleted ? 'success' : 'error',
                'message' => $deleted ? 'Product deleted' : 'Product not found'
            ]));
    }
}
