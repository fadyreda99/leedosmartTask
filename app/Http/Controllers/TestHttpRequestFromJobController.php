<?php

namespace App\Http\Controllers;

use App\Console\Commands\GetDataFromExternalApiCommand;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestHttpRequestFromJobController extends Controller
{
     private $testHttpResult;
     public function __construct(GetDataFromExternalApiCommand $command)
     {
         $this->testHttpResult = $command;
     }

    public function testHttpRequestFromJob(){
      dd($this->testHttpResult->handle());
    }
}
