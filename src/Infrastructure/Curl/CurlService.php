<?php

namespace App\Infrastructure\Curl;

use App\Infrastructure\Curl\Strategy\CurlMethodStrategy;
use CurlHandle;

class CurlService
{

  private string $url;

  private array $data;

  private CurlMethodStrategy $curlMethodStrategy;

  private CurlHandle $curlHandle;


  public function doRequest()
  {
    $this->curlHandle = curl_init();
    curl_setopt($this->curlHandle, CURLOPT_URL, $this->url);
    curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);

    return $this->curlMethodStrategy->doRequest($this);
  }

  public function getUrl(): string
  {
    return $this->url;
  }

  public function setUrl(string $url): self
  {
    $this->url = $url;
    return  $this;
  }

  public function getData(): array
  {
    return $this->data;
  }

  public function setStrategy(CurlMethodStrategy $curlMethodStrategy): self
  {
    $this->curlMethodStrategy = $curlMethodStrategy;
    return $this;
  }

  public function setData(array $data): self
  {
    $this->data = $data;
    return  $this;
  }

  public function getCurlhandle(): CurlHandle
  {
    return $this->curlHandle;
  }
}
