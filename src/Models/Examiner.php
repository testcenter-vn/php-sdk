<?php

namespace Testcenter\Testcenter\Models;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Testcenter\Testcenter\Exceptions\TestcenterException;
use Testcenter\Testcenter\Shared\StatusCode;
use Testcenter\Testcenter\Utils\ExaminerClient;
use Testcenter\Testcenter\Utils\TestcenterClient;

class Examiner
{
    public $id;
    public $fullname;
    public $username;
    public $lang;
    public $email;
    public $companyName;
    public $companyLogoURL;
    public $credit;
    public $customerCode;
    private ExaminerClient $client;

    public function __construct(ExaminerClient $client)
    {
        $this->client = $client;
        $this->getInfo();
    }

    public function getInfo()
    {
        $response = $this->client->request('GET', 'user');

        if ($response->user) {
            $this->id = isset($response->user->id) ? $response->user->id : null;
            $this->fullname = isset($response->user->fullname) ? $response->user->fullname : null;
            $this->username = isset($response->user->username) ? $response->user->username : null;
            $this->lang = isset($response->user->lang) ? $response->user->lang : null;
            $this->email = isset($response->user->email) ? $response->user->email : null;
            $this->companyName = isset($response->user->company_name) ? $response->user->company_name : null;
            $this->companyLogoURL = isset($response->user->company_logo_url) ? $response->user->company_logo_url : null;
            $this->credit = isset($response->user->credit) ? $response->user->credit : null;
            $this->customerCode = isset($response->user->customer_code) ? $response->user->customer_code : null;
        }
    }

    public function getTestLink($testCampaignId, $requestId, $fullname, $email, $phone, $position, $group, $identifyCode, $extraData)
    {
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
        $response = $this->client->request('POST', "test-campaigns/{$testCampaignId}/trigger-init-link", [
            'json' => $data
        ]);
        return $response->link;
    }

    public function getActiveTests()
    {
        $response = $this->client->request('GET', 'tests/all');
        return new Tests($response->tests);
    }

    public function getActiveTestCampaigns()
    {
        $response = $this->client->request('GET', 'test-campaigns/all');
        return new TestCampaigns($response->testCampaigns);
    }

    public function getTestCampaign($id)
    {
        $response = $this->client->request('GET', 'test-campaigns/' . $id . '/?sdk=1');
        return new TestCampaign($response->test_campaign);
    }
}
