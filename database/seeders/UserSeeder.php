<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'faculty']);
        Role::firstOrCreate(['name' => 'student']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@university.edu'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin123'),
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&h=200&fit=crop',
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $faculty = User::firstOrCreate(
            ['email' => 'john@university.edu'],
            [
                'name' => 'Professor John',
                'password' => bcrypt('faculty123'),
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop',
            ]
        );
        if (!$faculty->hasRole('faculty')) {
            $faculty->assignRole('faculty');
        }

        if (User::role('student')->count() === 0) {
            User::factory(10)->create()->each(function ($user) {
                $user->assignRole('student');
            });
        }
    }
}
