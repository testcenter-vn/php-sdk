<?php

namespace Testcenter\Testcenter\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Testcenter\Testcenter\Exceptions\ServerErrorException;
use Testcenter\Testcenter\Exceptions\TestcenterException;
use Testcenter\Testcenter\Exceptions\TokenInvalidException;
use Testcenter\Testcenter\Exceptions\DataException;
use Testcenter\Testcenter\Shared\StatusCode;

class TestcenterClient
{
    protected Client $client;

    public function request($method, $path, $option = [])
    {
        $client = $this->client;
        try {
            $response = $client->request($method, $path, $option);
            $response = json_decode($response->getBody());
            if (($response->statusCode ?? 0) == StatusCode::SUCCESS || !empty($response->success)) {
                return $response;
            }
            throw new DataException($response->message ?? 'Đã có lỗi xảy ra');
        } catch (GuzzleException $e) {
            if ($e instanceof RequestException) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();

                $data = json_decode($response->getBody());
                $message = $data->message ?? 'Đã có lỗi xảy ra';
                if ($statusCode == 401) {
                    throw new TokenInvalidException($message);
                } else if ($statusCode == 500) {
                    throw new ServerErrorException($message);
                }
            }
            throw new TestcenterException($e->getMessage());
        }
    }
}
