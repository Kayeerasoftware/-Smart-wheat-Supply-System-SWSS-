<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
<<<<<<< HEAD
            CategorySeeder::class,
            WarehouseSeeder::class,
=======
            SupplierSeeder::class,
            RawMaterialSeeder::class,
            ProductionLineSeeder::class,
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
        ]);
    }
}