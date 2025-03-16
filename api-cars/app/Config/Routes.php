<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/brands', 'Cars::getAllBrands'); //List all brands
$routes->get('/brands/(:num)/models', 'Cars::getModelsByBrand/$1'); //List all models of the brand
$routes->post('/brands', 'Cars::AddBrand'); //Add new brands. A brand name must be unique.
$routes->post('/brands/(:num)/models', 'Cars::addModelToBrand/$1'); //Add new models to a brand. A model name must be unique inside a brand.
$routes->put('/models/(:num)', 'Cars::updatePrice/$1'); //Edit (update) the average price of a model.
$routes->get('/models', 'Cars::listModels'); //List all models.