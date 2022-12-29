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
        Permission::create(['name' => 'Consultar principal']);
        Permission::create(['name' => 'Revisar tarjeta']);
        Permission::create(['name' => 'Crear targeta']);
        Permission::create(['name' => 'Editar targeta']);
        Permission::create(['name' => 'Eliminar targeta']);
        Permission::create(['name' => 'Subir foto']);
        Permission::create(['name' => 'Imprimir tarjeta']);
        Permission::create(['name' => 'Consultar registos']);
        Permission::create(['name' => 'Descargar registros']);
        Permission::create(['name' => 'Crear usuario']);
        Permission::create(['name' => 'Editar usuario']);
        Permission::create(['name' => 'Eliminar usuario']);
        $user->givePermissionTo(['Consultar principal','Revisar tarjeta','Crear targeta','Editar targeta','Eliminar targeta','Subir foto','Imprimir tarjeta','Consultar registos','Descargar registros','Crear usuario','Editar usuario','Eliminar usuario']);
    }
}
