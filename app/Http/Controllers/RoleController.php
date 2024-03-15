<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:عرض صلاحية', ['only' => ['index']]);
        $this->middleware('permission:اضافة صلاحية', ['only' => ['create','store']]);
        $this->middleware('permission:تعديل صلاحية', ['only' => ['edit','update']]);
        $this->middleware('permission:حذف صلاحية', ['only' => ['destroy']]);
    }

    public function index()
    {
        $roles = Role::latest()->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::get();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
        session()->flash('add');
        return redirect()->route('roles.index');
    }

    public function show(Role $role)
    {
        $rolePermissionsNames = $role->permissions->pluck('name');
        return view('roles.show', compact('role', 'rolePermissionsNames'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name');
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Role $role, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'permission' => 'required',
        ]);

        if($role->name !== $validated['name']){
            $role->name = $validated['name'];
            $role->save();
        }
         // the assignRole  method will remove any permissions that are not in the given array
        $role->syncPermissions($validated['permission']);
        session()->flash('update');
        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        session()->flash('delete');
        return redirect()->route('roles.index');
    }
}
