<?php
namespace Testcenter\Testcenter\Utils;
use GuzzleHttp\Client;
use Testcenter\Testcenter\Shared\Signature;

class PartnerClient extends \Testcenter\Testcenter\Utils\TestcenterClient
{
    public $secretKey;
    public function __construct ()
    {
        $partnerAccessToken = config('testcenter.partner_access_token');
        $secretKey = config('testcenter.partner_secret_key');

        if (!$partnerAccessToken) {
            throw new TestcenterException("Chưa cài đặt mã Partner Access Token", 1);
        }
        if (!$secretKey) {
            throw new TestcenterException("Chưa cài đặt mã Partner Secret Key", 1);
        }
        $requestParams = [
            'base_uri' => config('testcenter.api_endpoint'),
            'headers' => [
                'PartnerAccessToken' => $partnerAccessToken
            ],
            'timeout' => 10
        ];
        $this->client = new Client($requestParams);
        $this->secretKey = $secretKey;
    }

    public function makeSignature ($verifyString)
    {
        return Signature::get($verifyString, $this->secretKey);
    }
}
