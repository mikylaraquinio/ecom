<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Database\Seeders\CategorySeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // ✅ Create the Farmsmart admin account
        User::updateOrCreate(
            ['email' => 'farmsmartadmin@gmail.com'], 
            [
                'name' => 'Farmsmartadmin',
                'password' => bcrypt('farm123456pogi'), 
                'role' => 'admin',
                'is_admin' => true,
            ]
        );

        // Run other seeders
        $this->call([
            CategorySeeder::class,
        ]);
    }
}
