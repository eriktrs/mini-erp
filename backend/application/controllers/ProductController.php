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
        $this->load->model('ProductVariation_model');
        $this->load->model('ProductStock_model');
        $this->load->helper(['url', 'api']);
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

            foreach ($products as &$product) {
                $variations = $this->ProductVariation_model->get_by_product($product['id']);
                foreach ($variations as &$variation) {
                    $variation['stock'] = $this->ProductStock_model->get_by_variation($variation['id']);
                }
                $product['variations'] = $variations;
            }

            return respondSuccess($this->output, ['products' => $products]);
        } catch (Exception $e) {
            return respondError($this->output, 'Internal Server Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /products/{id}
     */
    public function show($id)
    {
        if ($this->input->method() !== 'get') {
            return respondError($this->output, 'Method Not Allowed', 405);
        }

        $product = $this->Product_model->getById((int)$id);
        if (!$product) {
            return respondError($this->output, 'Product not found', 404);
        }

        $variations = $this->ProductVariation_model->get_by_product((int)$id);
        foreach ($variations as &$variation) {
            $variation['stock'] = $this->ProductStock_model->get_by_variation($variation['id']);
        }

        return respondSuccess($this->output, [
            'product' => $product,
            'variations' => $variations
        ]);
    }

    /**
     * POST /products
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

        $productId = $this->Product_model->create($data);
        if (!$productId) {
            return respondError($this->output, 'Failed to create product', 500);
        }

        if (!empty($input['variations'])) {
            foreach ($input['variations'] as $variation) {
                if (!isset($variation['name'])) {
                    continue;
                }

                $variationData = [
                    'product_id' => $productId,
                    'name' => htmlspecialchars(trim($variation['name']))
                ];

                $variationId = $this->ProductVariation_model->insert($variationData);

                if ($variationId && isset($variation['stock'])) {
                    $stockData = [
                        'variation_id' => $variationId,
                        'quantity' => (int)$variation['stock']
                    ];
                    $this->ProductStock_model->insert($stockData);
                }
            }
        }

        return respondSuccess($this->output, ['message' => 'Product created', 'id' => $productId], 201);
    }

    /**
     * PUT /products/{id}
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
        if (!$updated) {
            return respondError($this->output, 'Product not found', 404);
        }

        
        if (!empty($input['variations'])) {
            
            $this->ProductVariation_model->delete_by_product((int)$id);

            foreach ($input['variations'] as $variation) {
                if (!isset($variation['name'])) {
                    continue;
                }

                $variationData = [
                    'product_id' => $id,
                    'name' => htmlspecialchars(trim($variation['name']))
                ];

                $variationId = $this->ProductVariation_model->insert($variationData);

                if ($variationId && isset($variation['stock'])) {
                    $stockData = [
                        'variation_id' => $variationId,
                        'quantity' => (int)$variation['stock']
                    ];
                    $this->ProductStock_model->insert($stockData);
                }
            }
        }

        return respondSuccess($this->output, ['message' => 'Product updated']);
    }

    /**
     * DELETE /products/{id}
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
