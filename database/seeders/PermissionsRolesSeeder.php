<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use App\Models\Role;

class PermissionsRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criação das Roles
        $roleShopKeeper = Role::create(['name' => 'shopKeeper']);
        $roleClient = Role::create(['name' => 'user']);

        // Criação das Permissions
        $permissionSendMoney = Permission::create(['name' => 'send_money']);
        $permissionReceiveMoney = Permission::create(['name' => 'receive_money']);

        // Relacionamento de Permissions com Roles
        $roleShopKeeper->permissions()->attach([$permissionReceiveMoney->id]);
        $roleClient->permissions()->attach([$permissionReceiveMoney->id, $permissionSendMoney->id]);

        // Criação dos Usuários
        $shopKeeperUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'documentType' => 1,
            'documentNumber' => 1,
            'password' => bcrypt('password'),
            'role_id' => $roleShopKeeper->id
        ]);

        $clientUser = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'documentType' => 2,
            'documentNumber' => 123,
            'password' => bcrypt('password'),
            'role_id' => $roleClient->id
        ]);

        // Relacionamento de Permissions com Usuários
        $shopKeeperUser->permissions()->attach([$permissionReceiveMoney->id]);
        $clientUser->permissions()->attach([$permissionReceiveMoney->id, $permissionSendMoney->id]);
    }
}
