<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
Spatie\Permission\Models\Role::firstOrCreate(['name' => 'faculty', 'guard_name' => 'web']);
Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

$u = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@university.edu',
    'password' => Illuminate\Support\Facades\Hash::make('admin123'),
]);
$u->assignRole('admin');
echo "Created: " . $u->email . "\n";
