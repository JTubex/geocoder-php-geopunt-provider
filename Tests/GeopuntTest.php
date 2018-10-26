<?php

declare(strict_types=1);

/*
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Geocoder\Provider\Geopunt\Tests;

use Geocoder\IntegrationTest\BaseTestCase;
use Geocoder\Provider\Geopunt\Geopunt;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

class GeopuntTest extends BaseTestCase
{
    protected function getCacheDir()
    {
        return __DIR__.'/.cached_responses';
    }

    /**
     * @expectedException \Geocoder\Exception\UnsupportedOperation
     * @expectedExceptionMessage The Geopunt provider does not support IP addresses, only street addresses.
     */
    public function testGeocodeWithLocalhostIPv4()
    {
        $provider = new Geopunt($this->getMockedHttpClient(), 'Geocoder PHP/Geopunt Provider/Geopunt Test');
        $provider->geocodeQuery(GeocodeQuery::create('127.0.0.1'));
    }

    /**
     * @expectedException \Geocoder\Exception\UnsupportedOperation
     * @expectedExceptionMessage The Geopunt provider does not support IP addresses, only street addresses.
     */
    public function testGeocodeWithLocalhostIPv6()
    {
        $provider = new Geopunt($this->getMockedHttpClient(), 'Geocoder PHP/Geopunt Provider/Geopunt Test');
        $provider->geocodeQuery(GeocodeQuery::create('::1'));
    }

    /**
     * @expectedException \Geocoder\Exception\UnsupportedOperation
     * @expectedExceptionMessage The Geopunt provider does not support IP addresses, only street addresses.
     */
    public function testGeocodeWithRealIPv6()
    {
        $provider = new Geopunt($this->getMockedHttpClient(), 'Geocoder PHP/Geopunt Provider/Geopunt Test');
        $provider->geocodeQuery(GeocodeQuery::create('::ffff:88.188.221.14'));
    }

    public function testReverseQuery()
    {
        $provider = new Geopunt($this->getHttpClient(), 'Geocoder PHP/Geopunt Provider/Geopunt Test');
        $results = $provider->reverseQuery(ReverseQuery::fromCoordinates(50.991974, 5.351705)->withLimit(1));

        $this->assertInstanceOf('Geocoder\Model\AddressCollection', $results);
        $this->assertCount(1, $results);

        /** @var \Geocoder\Model\Address $result */
        $result = $results->first();
        $this->assertInstanceOf('\Geocoder\Model\Address', $result);
        $this->assertEquals('1', $result->getStreetNumber());
        $this->assertEquals('Trambergstraat', $result->getStreetName());
        $this->assertEquals('3520', $result->getPostalCode());
        $this->assertEquals('Zonhoven', $result->getLocality());
    }

    public function testGeocodeQuery()
    {
        $provider = new Geopunt($this->getHttpClient(), 'Geocoder PHP/Geopunt Provider/Geopunt Test');
        $results = $provider->geocodeQuery(GeocodeQuery::create('Trambergstraat 1, 3520 Zonhoven')->withLimit(1));

        $this->assertInstanceOf('Geocoder\Model\AddressCollection', $results);
        $this->assertCount(1, $results);

        /** @var \Geocoder\Model\Address $result */
        $result = $results->first();
        $this->assertInstanceOf('\Geocoder\Model\Address', $result);
        $this->assertEquals(50.991974, $result->getCoordinates()->getLatitude(), '', 0.00001);
        $this->assertEquals(5.351705, $result->getCoordinates()->getLongitude(), '', 0.00001);
        $this->assertEquals('1', $result->getStreetNumber());
        $this->assertEquals('Trambergstraat', $result->getStreetName());
        $this->assertEquals('3520', $result->getPostalCode());
        $this->assertEquals('Zonhoven', $result->getLocality());
    }
}