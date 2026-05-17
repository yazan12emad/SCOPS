<?php
namespace App\Http\Controllers;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    // Returns paginated list of services (10 per page)
    // If category_id is provided in the request, filter services by that category
    public function index(Request $request){
        $query = Service::with('category'); // load service with its category data

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id); // filter by category
        }

        $service = $query->paginate(10); // get 10 services per page
        return $this->success($service, 'Service fetched');
    }

    // Returns a single service by its ID
    // If not found, return 404 error
    public function show($id){
        $service = Service::with(['category', 'plans'])->find($id);
        if(!$service){
            return $this->error('Service not found', 404);
        }
        return $this->success($service, 'Service fetched');
    }
}
