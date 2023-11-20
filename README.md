## Requirements
1. <code>"php": "^8.1"</code>
2. Installed composer
3. Installed docker and docker-compose

## Setup process
1. Clone the repository
2. Run <code>composer install</code>
3. Run migrations <code>./vendor/bin/sail artisan migrate</code>
4. Run seeders <code>./vendor/bin/sail artisan db:seed</code>

## Database diagram
<code>https://dbdiagram.io/d/factoryEcommerce-65539ac87d8bbd64652caa2d</code>

## API Endpints
Postman collection export <code>https://github.com/tarikc91/factory-ecommerce/blob/master/FactoryEcommerce.postman_collection.json</code>
1. <code>POST /api/register</code>
2. <code>POST /api/login</code>
3. <code>GET /api/products</code>
4. <code>GET /api/products/{product}</code>
5. <code>GET /api/categories/{category}/products</code>
6. <code>POST /api/orders</code>

## Tests
Run tests with <code>./vendor/bin/sail phpunit</code>
