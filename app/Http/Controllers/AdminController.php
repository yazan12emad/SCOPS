<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Service;
use App\Models\Subscription;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ApiResponse;
//----------------------------------------------------USERS-------------------------------------------------------------
    public function listUsers(Request $request){
        $query = User::query();
        if($request->has('search')){
            $query->where('first_name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%');// here to make the admin search the users
        }
        return $this->success($query->paginate(10), 'Users fetched');
    }

    //user activating and deactivating
    public function toggleUser($id){
        $user = User::find($id);
        if(!$user){ return $this->error('User not found', 404); }
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return $this->success($user, "User {$status} successfully");
    }

    //List the all the user subscription
    public function userSubscriptions($id){
        $user = User::find($id);
        if(!$user){ return $this->error('User not found', 404); }
        $subscriptions = Subscription::where('user_id', $id)->get();
        return $this->success($subscriptions, 'User subscriptions fetched');
    }

    //Add a new user
    public function addUser(Request $request){
        if(User::where('email', $request->email)->exists()){
            return $this->error('Email already exists', 400);
        }
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'phone'      => $request->phone,
            'role'       => $request->role ?? 'user',
            'is_active'  => true,
        ]);
        return $this->success($user, 'User created');
    }

    //Update first/last name , email, phone and the role of teh user
    public function updateUser(Request $request, $id){
        $user = User::find($id);
        if(!$user){ return $this->error('User not found', 404); }
        $user->update([
            'first_name' => $request->first_name ?? $user->first_name,
            'last_name'  => $request->last_name ?? $user->last_name,
            'email'      => $request->email ?? $user->email,
            'phone'      => $request->phone ?? $user->phone,
            'role'       => $request->role ?? $user->role,
        ]);
        return $this->success($user, 'User updated');
    }

    //Reset pasword for one user
    public function resetPassword(Request $request, $id){
        $user = User::find($id);
        if(!$user){
            return $this->error('User not found', 404);
        }
        $user->update(['password' => bcrypt($request->password)]);
        return $this->success(null, 'Password reset successfully');
    }

    //Delete one user
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        $user->delete();
        return $this->success(null, 'User deleted');
    }
//----------------------------------------------------SERVICES----------------------------------------------------------
    public function listServices(){
        $services = Service::with('category')->paginate(10);
        return $this->success($services, 'Services fetched');
    }

    public function addService(Request $request){
        if(Service::where('name', $request->name)->exists()){
            return $this->error('Service already exists', 400);
        }
        $service = Service::create([
            'category_id'    => $request->category_id,
            'name'           => $request->name,
            'logo_url'       => $request->logo_url,
            'default_amount' => $request->default_amount,
            'billing_cycle'  => $request->billing_cycle,
            'description'    => $request->description,
        ]);
        return $this->success($service, 'Service created');
    }

    public function updateService(Request $request, $id){
        $service = Service::find($id);
        if(!$service){ return $this->error('Service not found', 404); }
        $service->update($request->all());
        return $this->success($service, 'Service updated');
    }

    public function deleteService($id){
        $service = Service::find($id);
        if(!$service){ return $this->error('Service not found', 404); }
        $service->delete();
        return $this->success(null, 'Service deleted');
    }
//----------------------------------------------------STATISTICS--------------------------------------------------------
    public function statistics(){
        $stats = [
            'total_users'          => User::count(),
            'active_users'         => User::where('is_active', true)->count(),
            'total_subscriptions'  => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_services'       => Service::count(),
        ];
        return $this->success($stats, 'Statistics fetched');
    }
}
