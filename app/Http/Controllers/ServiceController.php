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
    public function index(Request $request)
    {
        $query = Service::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->paginate(10);

        // No asset() here — Cloudinary already gives full URL
        $services->getCollection()->transform(function ($service) {
            return $service;
        });

        return $this->success($services, 'Services fetched');
    }

    // Returns a single service by its ID
    // If not found, return 404 error
    public function show($id)
    {
        $service = Service::with(['category', 'plans'])->findOrFail($id);

        return $this->success($service, 'Service fetched');
    }
}
