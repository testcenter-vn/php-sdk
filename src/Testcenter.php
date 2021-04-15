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

class Testcenter
{
	private $env;
	public function __construct()
	{
	    $this->env = new Environment();
	}

	public function getClient()
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
			]
    	];
    	$client = new Client($requestParams);
    	$client->secretKey = $secretKey;
    	return $client;
	}

    public function hello()
    {
    	return "Hello From Testcenter";
    }

    public function getPublicTest()
    {
    	$client = $this->getClient();
    	try {
    		$response = $client->request('GET', 'public-test');
    	} catch (ClientException $e) {
    		$statusCode = $e->getResponse()->getStatusCode();

    		if ($statusCode == 404) {
    			throw new TestcenterException('API không hợp lệ');
    		}
    		throw new TestcenterException('Đã có lỗi xảy ra');
    	}

    	$response = json_decode((string)$response->getBody());
    	$publicTests = new PublicTests($response->data);

    	return $publicTests;
    }

    public function getIntegrateUrl($callbackUrl, $remainCredit = 0)
    {
        $clientId = config('testcenter.client_id');
        $verifyString = "client_id={$clientId}&callback_url={$callbackUrl}&remain_credit={$remainCredit}";
        $secretKey = config('testcenter.partner_secret_key');
        $signature = Signature::get($verifyString, $secretKey);
        return config('testcenter.client_url') . '/auth/authorize/?client_id=' . $clientId . '&callback_url=' . $callbackUrl . '&remain_credit=' . $remainCredit . '&signature=' . $signature;
    }

    public function initTestCampaign($testType, $requestId, $partnerUserId, $testCampaignName, $partnerUserName)
    {
    	$client = $this->getClient();
    	$secretKey = $client->secretKey;
    	try {
    		$verifyString = "requestId={$requestId}&partnerUserId={$partnerUserId}&partnerUserName={$partnerUserName}&testCampaignName={$testCampaignName}";
    		$signature = Signature::get($verifyString, $secretKey);
    		$body = [
    			"requestId" => $requestId,
				"partnerUserId" => $partnerUserId,
				"partnerUserName" => $partnerUserName,
				"testCampaignName" => $testCampaignName,
				"signature" => $signature,
    		];
    		$response = $client->request('POST', "public-test/{$testType}/init-test-campaign", [
    			'json' => $body
    		]);

    		$response = json_decode((string)$response->getBody());
    		if ($response->statusCode == StatusCode::SUCCESS) {
    			return new TestCampaign($response);
    		}
    		throw new TestcenterException($response->message);

    	} catch (ClientException $e) {
    		$statusCode = $e->getResponse()->getStatusCode();

    		if ($statusCode == 404) {
    			throw new TestcenterException('API không hợp lệ');
    		}
    		throw new TestcenterException('Đã có lỗi xảy ra');
    	}
    }

    public function getTestLink($testCampaignId, $requestId, $fullname, $email, $phone, $position, $group, $identifyCode, $extraData)
    {
    	$client = $this->getClient();
    	$secretKey = $client->secretKey;
    	try {
    		$verifyString = "requestId={$requestId}&fullname={$fullname}&email={$email}&phone={$phone}&position={$position}&group={$group}&identifyCode={$identifyCode}&extraData={$extraData}";
    		$signature = Signature::get($verifyString, $secretKey);
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
    		$response = $client->request('POST', "public-test/{$testCampaignId}/trigger-init-link", [
    			'json' => $body
    		]);

    		$response = json_decode((string)$response->getBody());
    		if ($response->statusCode == StatusCode::SUCCESS) {
    			return $response->link;
    		}
    		throw new TestcenterException($response->message);
    	} catch (ClientException $e) {
    		$statusCode = $e->getResponse()->getStatusCode();

    		if ($statusCode == 404) {
    			throw new TestcenterException('API không hợp lệ');
    		}
    		throw new TestcenterException('Đã có lỗi xảy ra');
    	}
    }

    public function getTestCampaignStatus($testCampaignId)
    {
    	$client = $this->getClient();
    	$secretKey = $client->secretKey;
    	try {
    		$response = $client->request('GET', "public-test/{$testCampaignId}/testcampaign-status-check");
    		$response = json_decode((string)$response->getBody());
    		if ($response->statusCode == StatusCode::SUCCESS) {
    			return new TestCampaignStatus($response);
    		}
    		throw new TestcenterException($response->message);
    	} catch (ClientException $e) {
    		$statusCode = $e->getResponse()->getStatusCode();

    		if ($statusCode == 404) {
    			throw new TestcenterException('API không hợp lệ');
    		}
    		throw new TestcenterException('Đã có lỗi xảy ra');
    	}
    }

    public function getAccessToken($accessCode)
    {
    	$client = $this->getClient();
    	$secretKey = $client->secretKey;
    	try {
    		$body = [
    			'accessCode' => $accessCode
    		];
    		$response = $client->request('POST', "partner/get-access-token-from-access-code", [
    			'json' => $body
    		]);
    		$response = json_decode((string)$response->getBody());
    		if ($response->statusCode == StatusCode::SUCCESS) {
    			return $response->access_token;
    		}
    		throw new TestcenterException($response->message);
    	} catch (ClientException $e) {
    		$statusCode = $e->getResponse()->getStatusCode();

    		if ($statusCode == 404) {
    			throw new TestcenterException('API không hợp lệ');
    		}
    		throw new TestcenterException('Đã có lỗi xảy ra: '. $e->getMessage());
    	}
    }

    public function getExaminer($accessToken)
    {
    	return new Examiner($accessToken);
    }

}