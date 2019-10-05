<?php

namespace Digitonic\IexCloudSdk\Tests\ReferenceData;

use Digitonic\IexCloudSdk\Facades\ReferenceData\Tags;
use Digitonic\IexCloudSdk\Tests\BaseTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;

class TagsTest extends BaseTestCase
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

        $this->response = new Response(200, [], '[{"name": "nTihneoootcyrElgcelc "},{"name": "eute vosiDitsribcSinr"},{"name": "neolgtT lcHyaehho"},{"name": " esSarliemirmCceovc"},{"name": "Seteicvln Irdusairs"},{"name": "ecanFni"},{"name": "ersuiPsodtc seIsrn"},{"name": "irTtsaanronpot"},{"name": "ociSseecl ynTerhgov"},{"name": "ifauraPtMdgrcenuuncor "},{"name": "re TaitdalRe"},{"name": "seCerivuSec srmno"},{"name": "rr-segnlEyaeNon niM"},{"name": "ilitsteUi"},{"name": "Meoasulcsnlie"},{"name": "ehrlcSi esavteH"},{"name": "mobsDueCsaurenlr "},{"name": "-uosrouDne snNblrCaem"},{"name": "oitmnuaCcoinms"},{"name": "iMarEyenlrnegs "},{"name": "mrnGenevot"}]');
    }

    /** @test */
    public function it_can_query_the_tags_endpoint()
    {
        $mock = new MockHandler([$this->response]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $iexApi = new \Digitonic\IexCloudSdk\Client($client);

        $tags = new \Digitonic\IexCloudSdk\ReferenceData\Tags($iexApi);

        $response = $tags->send();
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertCount(21, $response);
        $this->assertEquals('nTihneoootcyrElgcelc ', $response->first()->name);
    }

    /** @test */
    public function it_can_call_the_facade()
    {
        $this->setConfig();

        Tags::shouldReceive('send')
            ->once()
            ->andReturn(collect(json_decode($this->response->getBody()->getContents())));

        $response = Tags::send();

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertCount(21, $response);
        $this->assertEquals('nTihneoootcyrElgcelc ', $response->first()->name);

    }
}