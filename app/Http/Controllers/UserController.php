<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:قائمة المستخدمين', ['only' => ['index']]);
        $this->middleware('permission:اضافة مستخدم', ['only' => ['create','store']]);
        $this->middleware('permission:تعديل مستخدم', ['only' => ['edit','update']]);
        $this->middleware('permission:حذف مستخدم', ['only' => ['destroy']]);
    }

    public function index()
    {
        //$this->authorize('قائمة المستخدمين');
        $users = User::latest()->get();
        return view('users.index',compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name');
        return view('users.create',compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'status' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles_name' => 'required|array', // Ensure it's an array
            'roles_name.*' => 'exists:roles,name',// Check if each role name exists in the roles table
        ]);
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->assignRole($validated['roles_name']);
        session()->flash('add');
        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name')->all();
        $userRole = $user->roles->pluck('name')->all();

        return view('users.edit',compact('user','roles','userRole'));
    }
    public function update(User $user, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'status' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
        if(!empty($validated['password'])){
            $validated['password'] = Hash::make($validated['password']);
        }
        // If the 'password' field is empty,removes the 'password' field from the $validated array
        else{
            $validated = Arr::except($validated,array('password'));
        }
        $user->update($validated);
        // the assignRole  method will remove any roles that are not in the given array
        $user->assignRole($validated['roles']);
        session()->flash('update');
        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();
        session()->flash('delete');
        return redirect()->route('users.index');
    }
}
