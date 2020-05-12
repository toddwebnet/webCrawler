<?php

namespace App\Jobs;

use App\Helpers\ArrayHelpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class TestLogJob extends BaseJob
{

    private $timestamp;
    private $logStatement;

    /**
     * TestLogJob constructor.
     * @param array $args
     * @throws Exception
     */
    public function __construct($args)
    {
        if (!ArrayHelpers::keysInArray(['logStatement'], $args)) {
            throw new Exception('keys not found in args for construction of ' . get_class($this));
        }

        $this->logStatement = $args['logStatement'];
        $this->timestamp = Carbon::now()->toDateTimeString();

    }

    public function handle()
    {
        Log::info($this->logStatement . ' stored at: ' . $this->timestamp);
    }

}
