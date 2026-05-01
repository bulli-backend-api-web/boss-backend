<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

    protected $table = 'products';

    public function __construct() {
        parent::__construct();
    }

    public function find(int $id): ?array {
        $row = $this->db->get_where($this->table, ['id' => $id])->row_array();
        return $row ?: null;
    }

    public function paginate(int $page = 1, int $perPage = 15, ?string $search = null): array {
        $offset = ($page - 1) * $perPage;
        $this->db->from($this->table);

        if ($search) {
            $this->db->like('name', $search);
        }

        $total = $this->db->count_all_results('', false);

        $this->db->limit($perPage, $offset);
        $items = $this->db->get()->result_array();

        return [
            'items'        => $items,
            'total'        => $total,
            'page'         => $page,
            'per_page'     => $perPage,
            'total_pages'  => (int) ceil($total / $perPage),
        ];
    }

    public function create(array $data): int {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return (int) $this->db->insert_id();
    }

    public function update(int $id, array $data): bool {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id)->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

    public function delete(int $id): bool {
        $this->db->delete($this->table, ['id' => $id]);
        return $this->db->affected_rows() > 0;
    }
}
