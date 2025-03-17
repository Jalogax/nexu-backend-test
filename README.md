# Nexu-backend-test
This repository contains the development for a Nexu backend test implemented by José Gaxiola, which is an API developed with CodeIgniter 4 using MySQL.

## Building
First, we need to download the Codeigniter API to start with our development, and there are two ways to do it: using compose or manually. In my case, I did it  manually but I am going to explain both:

### Compose installation
In the folder above your project root:
```bash
composer create-project codeigniter4/appstarter api-cars
```
The command above will create an “api-cars” folder.

Note: If you omit the “api-cars” argument, the command will create an “appstarter” folder, which can be renamed as appropriate.

### Manual installation
Download the latest version (https://codeigniter.com/download), and extract it to become your project root.

Note: Develop the app inside the app folder, and the public folder will be the public-facing document root. Do not change anything inside the system folder!

### Server
To use CodeIgniter, we need a web server, in this case I used XAMPP (https://www.apachefriends.org/es/download.html) with PHP Version 8.1.25. 
XAMPP is a completely free and easy-to-install Apache distribution that includes MariaDB, PHP, and Perl. The XAMPP installation package has been designed to be incredibly easy to install and use.

### Database
In the same way, I used MySQL to develop the database part. It is included in XAMPP instalation, so is very easy to use.
I created a database called "db_cars" with two tables: "tb_brands" and "tb_models" that help with the solution of this test.

```sql
CREATE DATABASE db_cars;
-- Create database.
CREATE DATABASE IF NOT EXISTS db_cars;

USE db_cars;

-- Table brands.
CREATE TABLE `tb_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `average_price` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `unique_name` UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table models.
CREATE TABLE `tb_models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_brand` int(11) NOT NULL,
	FOREIGN KEY (id_brand) REFERENCES tb_brands(id),
  `name` varchar(50) NOT NULL,
  `average_price` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

Note: You can find the complete code (tables and inserts) for the database in the file "mysql-database-sql" of this repository.

### API
In our API, we only have to configurate our database configuration file, routes, controllers and models.

#### Database configutarion file
Firstable, as we are developing this API in a local server, we have to add our database credentials to our API. For this, we have to modify the file "\app\Config\Database.php", it have to look something like this:
```php
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'db_cars',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];
```
Note: CodeIgniter uses a default public array to define our database credentials. In this case, we only need to connect to one database, so we'll use this array, we only modify the credentials.

#### Routes
In CodeIgniter 4, routes exist to define how URLs map to specific controllers and methods in your application. They help manage how HTTP requests are processed and allow for clean, user-friendly URLs, so you can find this file in the route "app\Config\Routes.php".

In this case, we only need six routes, so it can look something like this:
```php
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/brands', 'Cars::getAllBrands'); 
$routes->get('/brands/(:num)/models', 'Cars::getModelsByBrand/$1'); 
$routes->post('/brands', 'Cars::AddBrand');
$routes->post('/brands/(:num)/models', 'Cars::addModelToBrand/$1'); 
$routes->put('/models/(:num)', 'Cars::updatePrice/$1');
$routes->get('/models', 'Cars::listModels');
```

#### Controllers
As you can see in the routes, I used a controller called "Cars", and it was created with the next command in the terminal:
```bash
php spark make:controller Cars
```
This command creates a new file in the "app\Controllers\Cars.php" path of our API. In this file, we code all the functions we use when consuming each of the previously defined endpoints. This is an example of one if this functions:
```php
<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\BrandsModel;
use App\Models\ModelsModel;

class Cars extends ResourceController {
    public function getAllBrands(){
        $brandMondel = new BrandsModel();
        $brands = $brandMondel->getAllBrands();
        return $this->respond($brands);
    }
    ....
```
Note: You can see all the functions in the source code of the API in this repository.

#### Models
Now, we need to define the models that help us with all de consults for the database. For this, we need to create the models, in this case I used two: "BrandsModel" and "ModelsModel" each one consults one of the tables of the database. For this, we can do it with the following commands:
```bash
php spark make:model BrandsModel
```
```bash
php spark make:model ModelsModel
```
This command creates a new file in our API's "app\Models\BrandsModel.php" path. In this file, we code all the functions the controller uses to consult to database. Here's an example of one of these functions:
```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandsModel extends Model {
    protected $table      = 'tb_brands'; // Table name in MySQL
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'average_price']; // Fields

    public function getAllBrands() {
        return $this->findAll();
    }
```
Note: You can see all the functions of the models in the source code of the API.

#### Last configuration (optional)
If you want to remove "public/" from your API URL in CodeIgniter, create a file called ".htaccess" with this content:
```
<IfModule mod_rewrite.c>
RewriteEngine On

# Redirect all traffic to the public/ folder
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```
This automatically redirects all requests to the public/ folder without it appearing in the URL.

## Running
To run our API, we just need to have our API in the web root directory of the Apache server, by default on Windows it is in the path "C:\xampp\htdocs\". Once our API is installed correctly, we can consume it using our favorite browser or using Postman.

## Testing
To test, we can consume all the endpoints that we created and see the response in our browser or using Postman.

You can look the folder "unit-test" to see all the test that I did to this API consuming each endpoint developed.

#### 1.- GET /brands 
List all brands.

    http://localhost/api-cars/brands

#### 2.- GET    /brands/:id/models 
List all models of the brand.

    http://localhost/api-cars/brands/52/models

#### 3.- POST   /brands
Add new brands. A brand name must be unique. If a brand name is already in use return a response code and error message.

    http://localhost/api-cars/brands

#### 4.- POST   /brands/:id/models
Add new models to a brand.
A model name must be unique inside a brand. If the brand id doesn't exist return a response code and error message. If the model name already exists for that brand return a response code and error message reflecting it. Average price is optional, if supply it must be greater than 100,000.

    http://localhost/api-cars/brands/3/models

#### 5.- PUT    /models/:id
Update the average price of a model. The average_price must be greater then 100,000.

    http://localhost/api-cars/models/15

Body:

    {"average_price": 312123}

#### 6.- GET    /models
List all models.
If greater param is included show all models with average_price greater than the param. If lower param is included show all models with average_price lower than the param.

    http://localhost/api-cars/models?greater=380000&lower=400000

## Deploy
#### Soon!

## Personal opinion
I liked to put into practice my domain in PHP and MySQL. It looks easier but when I was developing I had to pay attention to details and develop a good solution using the best practices. The API can be improved by adding a complete security part. I hope you like it as me doing it. :)