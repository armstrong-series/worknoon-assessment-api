<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\RoleService;
use App\Enums\RoleEnum;

class UserSeeder extends Seeder
{
    public function __construct(
        private RoleService $roleService
    ) {}

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@worknoon.io',],
            [
                'name'              => 'Joan Sanders',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $this->roleService->assignRole(
            $admin,
            RoleEnum::ADMIN->value
        );

        $support = User::updateOrCreate(
            ['email' => 'support@worknoon.io',],
            [
                'name'              => 'Karen Borders',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $this->roleService->assignRole(
            $support,
            RoleEnum::SUPPORT->value
        );
    }
}
