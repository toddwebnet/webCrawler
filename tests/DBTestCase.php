<?php

namespace Tests;

abstract class DBTestCase extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();


    }

    protected function tearDown(): void
    {

//        if (defined('TEST_DB_RESPAWNED')) {
//            $dbPath = $this->dbPath();
//            if (file_exists($dbPath)) {
//                unlink($dbPath);
//            }
//        }
        parent::tearDown();
    }

}
