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

In order to simulate price list application add the header <code>X-Price-List</code> to the request with the ID of the desired price list.

1. <code>POST /api/register</code>
2. <code>POST /api/login</code>
3. <code>GET /api/products</code>
    - Filter by name <code>name=some+product</code>
    - Filter by cateories <code>categories=1,2,3</code>
    - Filter by price equals <code>price=100</code>
    - Filter by price higher or equals <code>price=100|</code>
    - Filter by price lower or equals <code>price=|100</code>
    - Filter by price between <code>price=100|200</code>
    - Sort by price <code>orderBy=price,[asc|desc]</code>
    - Sort by name <code>orderBy=name,[asc|desc]</code>
4. <code>GET /api/products/{product}</code>
5. <code>GET /api/categories/{category}/products</code>
6. <code>POST /api/orders</code>

## Tests
Run tests with <code>./vendor/bin/sail phpunit</code>
