<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Luna',
            'email' => 'luna@gmail.com',
            'password' => bcrypt('12345678'),
            'status' => 'مفعل'
        ]);
        $role = Role::create(['name' => 'super admin']);
        $permissions = Permission::pluck('id');
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);
    }
}
