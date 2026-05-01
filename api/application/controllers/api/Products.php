<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Base_API_Controller.php';

/**
 * Products API Controller
 *
 * GET    /v1/products          → index()   — list all
 * GET    /v1/products/{id}     → show()    — single product
 * POST   /v1/products/store    → store()   — create
 * POST   /v1/products/update/{id} → update() — update
 * POST   /v1/products/delete/{id} → destroy()— delete
 */
class Products extends Base_API_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('api/Product_model');
        $this->require_auth(); // All product endpoints require a valid token
    }

    /** GET /v1/products */
    public function index() {
        $page     = (int) ($this->input->get('page') ?: 1);
        $per_page = (int) ($this->input->get('per_page') ?: 15);
        $search   = $this->input->get('search');

        $result = $this->Product_model->paginate($page, $per_page, $search);
        $this->success($result);
    }

    /** GET /v1/products/{id} */
    public function show(int $id) {
        $product = $this->Product_model->find($id);
        if (!$product) $this->error('Product not found.', 404);
        $this->success($product);
    }

    /** POST /v1/products/store */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->error('Method not allowed.', 405);

        $input = $this->input_json();
        $rules = [
            ['field' => 'name',  'label' => 'Name',  'rules' => 'required|max_length[200]'],
            ['field' => 'price', 'label' => 'Price', 'rules' => 'numeric'],
        ];

        $_POST = $input; // CI uses $_POST for form_validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules($rules);

        if (!$this->form_validation->run()) {
            $this->error('Validation failed.', 422, $this->form_validation->error_array());
        }

        $id = $this->Product_model->create([
            'name'        => trim($input['name']),
            'description' => trim($input['description'] ?? ''),
            'price'       => (float) ($input['price'] ?? 0),
            'status'      => isset($input['status']) ? (int) $input['status'] : 1,
        ]);

        $product = $this->Product_model->find($id);
        $this->success($product, 'Product created.', 201);
    }

    /** POST /v1/products/update/{id} */
    public function update(int $id) {
        $product = $this->Product_model->find($id);
        if (!$product) $this->error('Product not found.', 404);

        $input = $this->input_json();
        $fields = [];
        if (isset($input['name']))        $fields['name']        = trim($input['name']);
        if (isset($input['description'])) $fields['description'] = trim($input['description']);
        if (isset($input['price']))       $fields['price']       = (float) $input['price'];
        if (isset($input['status']))      $fields['status']      = (int) $input['status'];

        if (empty($fields)) $this->error('No fields to update.', 422);

        $this->Product_model->update($id, $fields);
        $updated = $this->Product_model->find($id);
        $this->success($updated, 'Product updated.');
    }

    /** POST /v1/products/delete/{id} */
    public function destroy(int $id) {
        $product = $this->Product_model->find($id);
        if (!$product) $this->error('Product not found.', 404);

        $this->Product_model->delete($id);
        $this->success(null, 'Product deleted.');
    }
}
