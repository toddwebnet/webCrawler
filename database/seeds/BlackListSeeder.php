<?php

use App\Models\Blacklist;
use Illuminate\Database\Seeder;

class BlackListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($x = 0; $x < 10; $x++) {
            factory(Blacklist::class)->create();
        }
    }
}
