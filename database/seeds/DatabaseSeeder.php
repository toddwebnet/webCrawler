<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(UrlSeeder::class);
        $this->call(HtmlSeeder::class);
        $this->call(LinkSeeder::class);
        $this->call(BlackListSeeder::class);

//        $this->call(UrlSeeder::class);
//        $this->call(UrlSeeder::class);

        // $this->call(UsersTableSeeder::class);



    }
}
