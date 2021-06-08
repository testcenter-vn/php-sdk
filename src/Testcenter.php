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
use Testcenter\Testcenter\Utils\PartnerClient;

class Testcenter
{
	private $env;
	private PartnerClient $client;
	public function __construct()
	{
	    $this->env = new Environment();
	    $this->client = new PartnerClient();
	}

    public function hello()
    {
    	return "Hello From Testcenter";
    }

    public function getPublicTest()
    {
        $response = $this->client->request('GET', 'public-test');
    	$publicTests = new PublicTests($response->data);

    	return $publicTests;
    }

    public function getIntegrateUrl($callbackUrl, $remainCredit = 0)
    {
        $clientId = config('testcenter.client_id');
        $verifyString = "client_id={$clientId}&callback_url={$callbackUrl}&remain_credit={$remainCredit}";
        $signature = $this->client->makeSignature($verifyString);
        return config('testcenter.client_url') . '/auth/authorize/?client_id=' . $clientId . '&callback_url=' . $callbackUrl . '&remain_credit=' . $remainCredit . '&signature=' . $signature;
    }

    public function initTestCampaign($testType, $requestId, $partnerUserId, $testCampaignName, $partnerUserName)
    {
        $verifyString = "requestId={$requestId}&partnerUserId={$partnerUserId}&partnerUserName={$partnerUserName}&testCampaignName={$testCampaignName}";
        $signature = $this->client->makeSignature($verifyString);
        $body = [
            "requestId" => $requestId,
            "partnerUserId" => $partnerUserId,
            "partnerUserName" => $partnerUserName,
            "testCampaignName" => $testCampaignName,
            "signature" => $signature,
        ];
        $response = $this->client->request('POST', "public-test/{$testType}/init-test-campaign", [
            'json' => $body
        ]);
        return new TestCampaign($response);
    }

    public function getTestLink($testCampaignId, $requestId, $fullname, $email, $phone, $position, $group, $identifyCode, $extraData)
    {
        $verifyString = "requestId={$requestId}&fullname={$fullname}&email={$email}&phone={$phone}&position={$position}&group={$group}&identifyCode={$identifyCode}&extraData={$extraData}";
        $signature = $this->client->makeSignature($verifyString);
        $body = [
            'requestId' => $requestId,
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
            'position' => $position,
            'group' => $group,
            'identifyCode' => $identifyCode,
            'extraData' => $extraData,
            'signature' => $signature,
        ];
        $response = $this->client->request('POST', "public-test/{$testCampaignId}/trigger-init-link", [
            'json' => $body
        ]);
        return $response->link;
    }

    public function getTestCampaignStatus($testCampaignId)
    {
        $response = $this->client->request('GET', "public-test/{$testCampaignId}/testcampaign-status-check");
        return new TestCampaignStatus($response);
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
    	return new Examiner($accessToken);
    }
}
