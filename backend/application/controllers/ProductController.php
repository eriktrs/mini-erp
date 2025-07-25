<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Product Controller
 *
 * Handles CRUD operations for products including variations and stock.
 */
class ProductController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load required models
        $this->load->model('Product_model');
        $this->load->model('Variation_model');
        $this->load->model('Stock_model');
        // Load form and URL helpers
        $this->load->helper(['url', 'form']);
        // Load session library for flash messages
        $this->load->library('session');
    }

    /**
     * Display list of all products.
     */
    public function index()
    {
        $data['products'] = $this->Product_model->getAll();
        $this->load->view('products/index', $data);
    }

    /**
     * Show form to create a new product.
     */
    public function create()
    {
        $this->load->view('products/create');
    }

    /**
     * Handle product creation and save to database.
     */
    public function store()
    {
        // Validate form data
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Product Name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', 'Validation failed.');
            redirect('products/create');
        }

        // Prepare product data
        $productData = [
            'name'  => $this->input->post('name'),
            'price' => $this->input->post('price'),
        ];

        // Handle variations
        $variations = $this->input->post('variations'); // Expect array of variations
        $productData['variations'] = [];

        if (!empty($variations) && is_array($variations)) {
            foreach ($variations as $variation) {
                $productData['variations'][] = [
                    'name'      => $variation['name'],
                    'quantity'  => $variation['quantity'],
                ];
            }
        }

        // Insert product and related data
        $this->Product_model->create($productData);

        $this->session->set_flashdata('success', 'Product created successfully!');
        redirect('products');
    }

    /**
     * Show edit form for a product.
     * 
     * @param int $id Product ID
     */
    public function edit($id)
    {
        $data['product'] = $this->Product_model->getById($id);

        if (!$data['product']) {
            $this->session->set_flashdata('error', 'Product not found.');
            redirect('products');
        }

        $this->load->view('products/edit', $data);
    }

    /**
     * Update existing product and its variations.
     * 
     * @param int $id Product ID
     */
    public function update($id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Product Name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', 'Validation failed.');
            redirect('products/edit/' . $id);
        }

        $updateData = [
            'name'  => $this->input->post('name'),
            'price' => $this->input->post('price'),
            'variations' => $this->input->post('variations'),
        ];

        $this->Product_model->update($id, $updateData);

        $this->session->set_flashdata('success', 'Product updated successfully!');
        redirect('products');
    }

    /**
     * Delete a product by ID.
     * 
     * @param int $id Product ID
     */
    public function delete($id)
    {
        if ($this->Product_model->delete($id)) {
            $this->session->set_flashdata('success', 'Product deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete product.');
        }

        redirect('products');
    }
}
