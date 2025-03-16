<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelsModel extends Model
{
    protected $table = 'tb_models'; // Table name in MySQL
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'average_price', 'id_brand']; // Fields

    public function getModelsByBrand($id_brand) {
        return $this->select('id, name, average_price')
                ->where('id_brand', $id_brand)
                ->findAll();
    }

    public function insertModel($data) {
        return $this->insert($data);
    }

    public function getModel($id) {
        return $this->find($id);
    }

    public function updateAveragePrice($id, $data) {
        return $this->update($id, $data);
    }

    public function getFilteredModels($greater = null, $lower = null)
    {
        $builder = $this->builder();

        if ($greater !== null) {
            $builder->where('average_price >', $greater);
        }

        if ($lower !== null) {
            $builder->where('average_price <', $lower);
        }

        return $builder->get()->getResult();
    }
}
