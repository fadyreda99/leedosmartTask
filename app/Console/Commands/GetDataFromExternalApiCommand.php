<?php

namespace App\Console\Commands;

use App\Http\Controllers\TestHttpRequestFromJobController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetDataFromExternalApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getData:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get data from external api';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get('https://randomuser.me/api/');
        $result =$response['results'];
        info($result);
        return $result;
    }
}
