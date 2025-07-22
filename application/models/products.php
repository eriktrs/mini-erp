<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a new product along with its variations and stock.
     *
     * @param array $data Product data including variations and stock quantities.
     * @return int Inserted product ID.
     */
    public function create(array $data): int
    {
        // Insert product basic information
        $this->db->insert('products', [
            'name' => $data['name'],
            'price' => $data['price'],
        ]);

        $productId = (int) $this->db->insert_id();

        // Insert variations and stock if provided
        if (!empty($data['variations']) && is_array($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                $this->db->insert('variations', [
                    'product_id' => $productId,
                    'name' => $variation['name'],
                ]);
                $variationId = (int) $this->db->insert_id();

                $this->db->insert('stock', [
                    'variation_id' => $variationId,
                    'quantity' => (int) $variation['quantity'],
                ]);
            }
        }

        return $productId;
    }

    /**
     * Update a product and its variations including stock.
     *
     * @param int $id Product ID to update.
     * @param array $data Data to update.
     * @return bool True on success, false otherwise.
     */
    public function update(int $id, array $data): bool
    {
        // Update product information
        $this->db->where('id', $id);
        $updated = $this->db->update('products', [
            'name' => $data['name'],
            'price' => $data['price'],
        ]);

        // Update variations and stock if provided
        if (!empty($data['variations']) && is_array($data['variations'])) {
            foreach ($data['variations'] as $variation) {
                if (isset($variation['id'])) {
                    $this->db->where('id', $variation['id']);
                    $this->db->update('variations', [
                        'name' => $variation['name'],
                    ]);

                    $this->db->where('variation_id', $variation['id']);
                    $this->db->update('stock', [
                        'quantity' => (int) $variation['quantity'],
                    ]);
                }
            }
        }

        return $updated;
    }

    /**
     * Retrieve all products with their variations and stock quantities.
     *
     * @return array List of products with nested variations.
     */
    public function getAll(): array
    {
        $products = $this->db->get('products')->result_array();

        foreach ($products as &$product) {
            $variations = $this->db->get_where('variations', ['product_id' => $product['id']])->result_array();

            foreach ($variations as &$variation) {
                $stock = $this->db->get_where('stock', ['variation_id' => $variation['id']])->row_array();
                $variation['quantity'] = $stock ? (int) $stock['quantity'] : 0;
            }

            $product['variations'] = $variations;
        }

        return $products;
    }
}
