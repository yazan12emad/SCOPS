<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;
    // Returns paginated list of services, filterable by category_id
    public function index(Request $request){
        $query = Service::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $service = $query->paginate(10);
        return $this->success($service , 'Service fetched ');
    }

    public function show($id){
        $service = Service::with('Category')->find($id);// id for category

        if(!$service){
            return $this->error('Service not found' , 404);
        }
        return $this->success($service , 'Service fetched');
    }
}
