<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;

class CategoryProductsController extends Controller
{
    /**
     * Gets paginated products for a category
     *
     * @param Category $category
     * @return ProductCollection
     */
    public function index(Category $category): ProductCollection
    {
        $products = $category->products()->paginate(25);

        return new ProductCollection($products);
    }
}
