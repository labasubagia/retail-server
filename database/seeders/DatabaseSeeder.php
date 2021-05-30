<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\ProductType;
use App\Models\Store;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        $this->makeUser();
    }

    private function makeUser()
    {
        $stores = Store::factory()->count(2)->create();
        User::factory(['email' => 'retail1@mail.com', 'type' => 'admin_retail', 'store_id' => null])->create();
        User::factory(['email' => 'customer1@mail.com', 'type' => 'customer', 'store_id' => null])->create();
        $stores->each(function ($store, $index) {
            $index += 1;
            $payload = [
                'email' => "store{$index}@mail.com",
                'type' => 'admin_store',
                'store_id' => $store->id,
            ];
            User::factory($payload)->create();
        });
    }
}
