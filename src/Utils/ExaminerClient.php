<?php
namespace Testcenter\Testcenter\Utils;
use GuzzleHttp\Client;

class ExaminerClient extends \Testcenter\Testcenter\Utils\TestcenterClient
{
    public function __construct ($accessToken)
    {
        $requestParams = [
            'base_uri' => config('testcenter.api_endpoint'),
            'headers' => [
                'Authorization' => "Bearer {$accessToken}"
            ]
        ];
        parent::__construct($requestParams);
    }
}
