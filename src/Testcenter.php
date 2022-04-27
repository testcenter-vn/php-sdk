<?php

namespace Testcenter\Testcenter;

use Testcenter\Testcenter\Shared\Environment;
use Testcenter\Testcenter\Shared\Signature;
use Testcenter\Testcenter\Shared\StatusCode;
use Testcenter\Testcenter\Models\Examiner;
use GuzzleHttp\Client;
use Testcenter\Testcenter\Models\PublicTests;
use Testcenter\Testcenter\Models\TestCampaignStatus;
use Testcenter\Testcenter\Models\TestCampaign;
use Testcenter\Testcenter\Exceptions\TestcenterException;
use GuzzleHttp\Exception\ClientException;
use Testcenter\Testcenter\Utils\ExaminerClient;
use Testcenter\Testcenter\Utils\PartnerClient;

class Testcenter
{
    private $env;
    private PartnerClient $client;

    public function __construct(Environment $environment, PartnerClient $partnerClient)
    {
        $this->env = $environment;
        $this->client = $partnerClient;
    }

    public function getIntegrateUrl($callbackUrl, $remainCredit = 0)
    {
        $clientId = config('testcenter.client_id');
        $verifyString = "client_id={$clientId}&callback_url={$callbackUrl}&remain_credit={$remainCredit}";
        $signature = $this->client->makeSignature($verifyString);
        return config('testcenter.client_url') . '/auth/authorize/?client_id=' . $clientId . '&callback_url=' . $callbackUrl . '&remain_credit=' . $remainCredit . '&signature=' . $signature;
    }

    public function getAccessToken($accessCode)
    {
        $body = [
            'accessCode' => $accessCode
        ];
        $response = $this->client->request('POST', "partner/get-access-token-from-access-code", [
            'json' => $body
        ]);
        return $response->access_token;
    }

    public function getExaminer($accessToken)
    {
        $examinerClient = new ExaminerClient($accessToken);

        return new Examiner($examinerClient);
    }
}
