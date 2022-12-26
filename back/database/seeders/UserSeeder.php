<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('admin123Admin'),
        ]);
        Permission::create(['name' => 'create cards']);
        Permission::create(['name' => 'ready cards']);
        Permission::create(['name' => 'update cards']);
        Permission::create(['name' => 'delete cards']);
        $user->givePermissionTo(['create cards','ready cards','update cards','delete cards']);

        Permission::create(['name' => 'create records']);
        Permission::create(['name' => 'ready records']);
        Permission::create(['name' => 'update records']);
        Permission::create(['name' => 'delete records']);
        $user->givePermissionTo(['create records','ready records','update records','delete records']);
    }
}
