<?php

namespace Digitonic\IexCloudSdk\Tests\DataApis;

use Digitonic\IexCloudSdk\Exceptions\WrongData;
use Digitonic\IexCloudSdk\Facades\DataApis\DataPoints;
use Digitonic\IexCloudSdk\Tests\BaseTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;

class DataPointsTest extends BaseTestCase
{
    /**
     * @var Response
     */
    private $response;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->response = new Response(200, [], '[{"key": "NEXTDIVIDENDDATE","weight": 1,"description": "","lastUpdated": "2019-08-09T08:50:31+00:00"},{"key": "ACCOUNTSPAYABLE","weight": 3000,"description": "Balance Sheet: accountsPayable","lastUpdated": "2019-09-30T08:08:13+00:00"},{"key": "ZIP","weight": 1,"description": "zip","lastUpdated": "2019-09-30T10:08:03+00:00"}]');
    }

    /** @test */
    public function it_should_fail_without_a_symbol()
    {
        $mock = new MockHandler([$this->response]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $iexApi = new \Digitonic\IexCloudSdk\Client($client);

        $dataPoints = new \Digitonic\IexCloudSdk\DataApis\DataPoints($iexApi);

        $this->expectException(WrongData::class);

        $dataPoints->send();
    }

    /** @test */
    public function it_can_query_the_data_points_endpoint()
    {
        $mock = new MockHandler([$this->response]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $iexApi = new \Digitonic\IexCloudSdk\Client($client);

        $dataPoints = new \Digitonic\IexCloudSdk\DataApis\DataPoints($iexApi);

        $response = $dataPoints->setSymbol('aapl')->send();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertCount(3, $response);
        $this->assertEquals('NEXTDIVIDENDDATE', $response->first()->key);
        $this->assertEquals('2019-08-09T08:50:31+00:00', $response->first()->lastUpdated);
    }

    /** @test */
    public function it_can_query_the_data_points_endpoint_with_key()
    {
        $mock = new MockHandler([
            new Response(200, [], '"2019-08-19"')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $iexApi = new \Digitonic\IexCloudSdk\Client($client);

        $dataPoints = new \Digitonic\IexCloudSdk\DataApis\DataPoints($iexApi);

        $response = $dataPoints->setSymbol('aapl')->setKey('NEXTDIVIDENDDATE')->send();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertCount(1, $response);
        $this->assertEquals('2019-08-19', $response->first());
    }

    /** @test */
    public function it_can_call_the_facade()
    {
        $this->app['config']->set('iex-cloud-sdk.base_url', 'https://cloud.iexapis.com/v1');
        $this->app['config']->set('iex-cloud-sdk.secret_key', 'KxDMt9GNVgu6fJUOG0UjH3d4kjZPTxFiXd5RnPhUD8Qz1Q2esNVIFfqmrqRD');
        $this->app['config']->set('iex-cloud-sdk.public_key', 'KxDMt9GNVgu6fJUOG0UjH3d4kjZPTxFiXd5RnPhUD8Qz1Q2esNVIFfqmrqRD');

        DataPoints::shouldReceive('setSymbol')
            ->once()
            ->andReturnSelf();

        DataPoints::setSymbol('aapl');
    }
}
