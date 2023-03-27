<?php

namespace App\Infrastructure\Curl\Strategy;

use App\Infrastructure\Curl\CurlService;

class CurlGetStrategy implements CurlMethodStrategy
{

  public function doRequest(CurlService $curlService): string
  {
    $curlHandle = $curlService->getCurlhandle();

    curl_setopt($curlHandle, CURLOPT_HTTPGET, true);

    $result = curl_exec($curlHandle);
    curl_close($curlHandle);

    if (!$result) {
      throw new \Exception('');
    }

    return $result;
  }
}
