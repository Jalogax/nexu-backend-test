<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandsModel extends Model
{
    protected $table      = 'tb_brands'; // Table name in MySQL
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'average_price']; // Fields

    public function getAllBrands() {
        return $this->findAll();
    }

    public function getBrandById($idBrand) {
        return $this->find($idBrand);
    }

    public function getBrandByName($name) {
        return $this->where('name', $name)->first();
    }

    public function insertBrand($data) {
        return $this->insert($data);
    }
}
