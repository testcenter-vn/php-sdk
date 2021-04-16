<?php

namespace Testcenter\Testcenter\Models;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Testcenter\Testcenter\Exceptions\TestcenterException;
use Testcenter\Testcenter\Shared\StatusCode;

class Examiner
{
    public $accessToken;
    public $id;
    public $fullname;
    public $username;
    public $lang;
    public $email;
    public $companyName;
    public $companyLogoURL;
    public $credit;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->getInfo();
    }

    public function getClient()
    {
        $requestParams = [
            'base_uri' => config('testcenter.api_endpoint'),
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}"
            ]
        ];
        $client = new Client($requestParams);
        return $client;
    }

    public function getInfo()
    {
        $client = $this->getClient();
        try {
            $response = $client->request('GET', 'user');
            $response = json_decode((string)$response->getBody());
            if ($response->user) {
                $this->id = isset($response->user->id) ? $response->user->id : null;
                $this->fullname = isset($response->user->fullname) ? $response->user->fullname : null;
                $this->username = isset($response->user->username) ? $response->user->username : null;
                $this->lang = isset($response->user->lang) ? $response->user->lang : null;
                $this->email = isset($response->user->email) ? $response->user->email : null;
                $this->companyName = isset($response->user->company_name) ? $response->user->company_name : null;
                $this->companyLogoURL = isset($response->user->company_logo_url) ? $response->user->company_logo_url : null;
                $this->credit = isset($response->user->credit) ? $response->user->credit : null;
            }
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
        try {
            $data = [
                'requestId' => $requestId,
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'position' => $position,
                'group' => $group,
                'identifyCode' => $identifyCode,
                'extraData' => $extraData,
            ];
            $response = $client->request('POST', "test-campaigns/{$testCampaignId}/trigger-init-link", [
                'json' => $data
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

    public function getActiveTests()
    {
        $client = $this->getClient();
        try {
            $response = $client->request('GET', 'tests/all');
            $response = json_decode((string)$response->getBody());
            if ($response->success) {
                return new Tests($response->tests);
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 404) {
                throw new TestcenterException('API không hợp lệ');
            }
            throw new TestcenterException('Đã có lỗi xảy ra');
        }
    }

    public function getActiveTestCampaigns()
    {
        $client = $this->getClient();
        try {
            $response = $client->request('GET', 'test-campaigns/all');
            $response = json_decode((string)$response->getBody());
            if ($response->success) {
                return new TestCampaigns($response->testCampaigns);
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 404) {
                throw new TestcenterException('API không hợp lệ');
            }
            throw new TestcenterException('Đã có lỗi xảy ra');
        }
    }

    public function getTestCampaign($id)
    {
        $client = $this->getClient();
        try {
            $response = $client->request('GET', 'test-campaigns/' . $id . '/?sdk=1');
            $response = json_decode((string)$response->getBody());
            if ($response->success) {
                return new TestCampaign($response->test_campaign);
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 404) {
                throw new TestcenterException('API không hợp lệ');
            }
            throw new TestcenterException('Đã có lỗi xảy ra');
        }
    }
}
