<?php

namespace App\Infrastructure\Curl\Strategy;

use App\Infrastructure\Curl\CurlService;

interface CurlMethodStrategy
{
  public function doRequest(CurlService $curlService);
}
