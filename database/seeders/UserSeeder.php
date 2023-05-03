<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Balance;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create Roles
        $roleShopKeeper = Role::create(['name' => 'shopkeeper']);
        $roleClient = Role::create(['name' => 'client']);

        // create Permissions
        $permissionSendMoney = Permission::create(['name' => 'send_money']);
        $permissionReceiveMoney = Permission::create(['name' => 'receive_money']);

        // create relationship of permissions with role
        $roleShopKeeper->permissions()->attach([$permissionReceiveMoney->id]);
        $roleClient->permissions()->attach([$permissionReceiveMoney->id, $permissionSendMoney->id]);

        // create users
        $shopKeeperUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'documentType' => 1,
            'documentNumber' => 74981225024, //By 4Devs
            'password' => bcrypt('password'),
            'role_id' => $roleShopKeeper->id
        ]);

        $clientUser = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'documentType' => 2, //By 4Devs
            'documentNumber' => 40493440097,
            'password' => bcrypt('password'),
            'role_id' => $roleClient->id
        ]);

        //create balance of users
        Balance::create([
            'user_id' => $shopKeeperUser->id,
            'amount'  => 0.00
        ]);

        Balance::create([
            'user_id' => $clientUser->id,
            'amount'  => 0.00
        ]);

        // create relationship of permissions with users
        $shopKeeperUser->permissions()->attach([$permissionReceiveMoney->id]);
        $clientUser->permissions()->attach([$permissionReceiveMoney->id, $permissionSendMoney->id]);
    }
}
