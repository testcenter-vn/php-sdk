<?php

namespace Testcenter\Testcenter\Tests\Unit;

use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Mockery;
use Testcenter\Testcenter\Models\Examiner;
use Testcenter\Testcenter\Shared\Environment;
use Testcenter\Testcenter\Tests\TestCase;
use Testcenter\Testcenter\Utils\ExaminerClient;
use Testcenter\Testcenter\Utils\PartnerClient;
use Illuminate\Support\Str;
use Testcenter\Testcenter\Testcenter as TestCenterController;
use Testcenter\Testcenter\Shared\Signature;

class TestCenter extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->partner = Mockery::mock(PartnerClient::class);
        $this->environment = Mockery::mock(Environment::class);
        $this->examinerClient = Mockery::mock(ExaminerClient::class);
        $this->testcenter = new TestCenterController(
            $this->environment,
            $this->partner
        );
    }

    public function testGetTestCampaign()
    {
        $accessToken = Str::random(10);
        $accessCode = Str::random(10);
        $body = [
            'statusCode' => 0,
            'access_token' => $accessToken
        ];
        $this->partner->shouldReceive('request')->once()->andReturn((object)$body);
        $response = $this->testcenter->getAccessToken($accessCode);
        $this->assertEquals($accessToken, $response);
    }

    public function testGetExaminer()
    {
        $response = [
            'user' => [
                'id' => $this->faker->randomDigit,
                'username' => $this->faker->userName,
                'fullname' => $this->faker->userName,
                'lang' => 'vi',
                'email' => $this->faker->email,
                'companyName' => $this->faker->company,
                'companyLogoURL' => null,
                'credit' => null,
                'customerCode' => null
            ]
        ];
        $this->examinerClient->shouldReceive('request')->once()->andReturn(json_decode(json_encode($response)));
        try {
            $examiner = new Examiner($this->examinerClient);
            $this->assertTrue(true);
        } catch (Mockery\Exception $exception) {
            $this->assertTrue(false);
        }
    }

    public function testGetIntegrateUrl()
    {
        $clientId = config('testcenter.client_id');
        $clientUrl = config('testcenter.client_url');
        $callbackUrl =  $this->faker->url;
        $remainCredit =  $this->faker->randomNumber;
        $verifyString = "client_id={$clientId}&callback_url={$callbackUrl}&remain_credit={$remainCredit}";
        $secretKey = config('testcenter.partner_secret_key');
        $signature = Signature::get($verifyString, $secretKey);
        $result = $clientUrl . '/auth/authorize/?client_id=' . $clientId . '&callback_url=' . $callbackUrl . '&remain_credit=' . $remainCredit . '&signature=' . $signature;
        $this->partner->shouldReceive('makeSignature')->with($verifyString)->andReturn($signature);
        $response = $this->testcenter->getIntegrateUrl($callbackUrl, $remainCredit);
        $this->assertEquals($result, $response);
    }
}
