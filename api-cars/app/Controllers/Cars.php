<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\BrandsModel;
use App\Models\ModelsModel;

class Cars extends ResourceController
{
    public function index(){
        // Default method.
    }
    
    public function getAllBrands(){
        $brandMondel = new BrandsModel();
        $brands = $brandMondel->getAllBrands();
        return $this->respond($brands);
    }

    public function getModelsByBrand($idBrand){
        $brandModel = new BrandsModel();
        $modelModel = new ModelsModel();

        $brand = $brandModel->getBrandById($idBrand);
        if (!$brand) {
            return $this->failNotFound('Brand not found.');
        }

        $models = $modelModel->getModelsByBrand($idBrand);
        return $this->respond($models);
    }

    public function addBrand(){
        $brandModel = new BrandsModel();
        
        // Receive input data.
        $data = [
            'name' => $name = $this->request->getPost('name'),
            'average_price' => $price = $this->request->getPost('average_price')
        ];

        // Validate if brand already exists.
        $brand = $brandModel->getBrandByName($data['name']);
        if ($brand) {
            return $this->failResourceExists('The brand name \'' . $data['name'] . '\' is already registered. Please type a new one.', 400);
        }
        
        // Insert new register.
        try {
            $result = $brandModel->insertBrand($data);
            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Brand added correctly.',
                'data' => $data
            ]);
        } catch (DatabaseException $e) {
            return $this->fail($result['message'], 400);
        }
    }

    public function addModelToBrand($idBrand){
        $brandModel = new BrandsModel();
        $modelModel = new ModelsModel();

        // Receive input parameters.
        $data = [
            'id_brand' => $idBrand,
            'name' => $name = $this->request->getPost('name'),
            'average_price' => $price = $this->request->getPost('average_price')
        ];

        // Validate brand existence.
        $brand = $brandModel->getBrandById($data['id_brand']);
        if (!$brand) {
            return $this->failNotFound('Brand not found.');
        }

        // Validate model existence in the brand.
        $models = $modelModel->getModelsByBrand($data['id_brand']);
        if ($models) {
            foreach ($models as $model) {
                if ($model['name'] == $data['name']) {
                    return $this->failResourceExists('The brand name \'' . $data['name'] . '\' is already registered for this brand. Please type a new one.');
                }
            }
        }
        
        // Validate average price, it must be null or greater than 100,000.
        if ($data['average_price'] != NULL && $data['average_price'] <= 100000) {
            return $this->failValidationErrors('The average price \'' . $data['average_price'] . '\' is invalid. Please type a new one greater than 100,000.');
        }
        
        $result = $modelModel->insertModel($data);

        return $this->respondCreated(['status' => 'success',
                'message' => 'Model added correctly.',
                'data' => $data]);
    }

    public function updatePrice($id){
        $modelModel = new ModelsModel();

        $data = $this->request->getJSON();

        $model = $modelModel->getModel($id);
        if ($model) {
            // Validate the necessary data of the input.
            if ($data->average_price) {
                // Validate average price greater than 100,000.
                if ($data->average_price <= 100000) {
                    return $this->failValidationErrors('The average price \'' . $data->average_price . '\' is invalid. Please type a new one greater than 100,000.');
                }
            
                if ($modelModel->updateAveragePrice($id, $data)) {
                    return $this->respond(['message' => 'Model updated successfully.']);
                } else {
                    return $this->failNotFound('Model not found.');
                }
            }
        } else {
            return $this->failNotFound('Brand not found.');
        }
        
        return $this->fail('Invalid data.');
    }

    public function listModels()
    {
        $modelModel = new ModelsModel();

        // Get parameters.
        $greater = $this->request->getGet('greater');
        $lower = $this->request->getGet('lower');

        $models = $modelModel->getFilteredModels($greater, $lower);

        // Return data.
        if (count($models) > 0) {
            return $this->respond($models);
        } else {
            return $this->failNotFound('No models found matching your criteria.');
        }
    }
}
