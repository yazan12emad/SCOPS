<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\ApiResponse;

class CategoryController extends Controller{
    use ApiResponse;
    public function index(){
        $categories = Category::all();
        return $this->success($categories , 'Category fetched successfully.');
    }
}
