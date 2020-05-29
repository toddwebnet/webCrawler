<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Blacklist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacklist', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('host', 2048);
            $table->timestamps();
        });

        foreach ([
                     'amazon.com',
                     'walmart.com',
                     'wikipedia.com',
                     'sina.com.cn',
                     'apple.com',
                     'sohu.com',
                     'bing.com',
                     'google.com',
                     'gmail.com',
                     'twitter.com',
                     'facebook.com',
                     'taoboa.com',
                     'ask.com',
                     'baidu.com',
                     'microsoft.com',
                     'qq.com',
                     'live.com',
                     'wikipedia.com',
                     'wikipedia.org',
                     'archive.org',

                 ] as $host) {
            \App\Models\Blacklist::create(['host' => trim(strtolower($host))]);

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blacklist');
    }
}
