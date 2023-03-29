<?php

namespace App\Infrastructure\Curl\Strategy;

use App\Infrastructure\Curl\CurlService;

class CurlPostStrategy implements CurlMethodStrategy {
	public function doRequest(CurlService $curlService) {
        $curlHandle = $curlService->getCurlhandle();

        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $curlService->getData());

        $result = curl_exec($curlHandle);
        curl_close($curlHandle);

        if (!$result) {
            throw new \Exception('');
        }

        return $result;
	}
}