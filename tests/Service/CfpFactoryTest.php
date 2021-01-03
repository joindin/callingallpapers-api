<?php
/**
 * Copyright (c) 2016-2016} Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2016-2016 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     01.03.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace CallingallpapersTest\Service;

use Callingallpapers\Api\Entity\Cfp;
use Callingallpapers\Api\Service\CfpFactory;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class CfpFactoryTest extends TestCase
{
    /**
     * @dataProvider ProvideStrings
     */
    public function testThatSettingNameWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setName($cfp, ['name' => $value]);

        self::assertEquals($expected, $cfp->getName());
    }

    public function testThatSettingNameFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();

        self::expectException(InvalidArgumentException::class);
        CfpFactory::setName($cfp, []);
    }

    /**
     * @dataProvider ProvideStrings
     */
    public function testThatSettingDescriptionWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setDescription($cfp, ['description' => $value]);

        self::assertEquals($expected, $cfp->getDescription());
    }

    public function testThatSettingDescriptionWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setDescription($cfp, []);

        self::assertEquals('', $cfp->getDescription());
    }

    /**
     * @dataProvider ProvideStrings
     */
    public function testThatSettingLocationWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setLocation($cfp, ['location' => $value]);

        self::assertEquals($expected, $cfp->getLocation());
    }

    public function testThatSettingLocationWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setLocation($cfp, []);

        self::assertEquals('', $cfp->getLocation());
    }

    /**
     * @dataProvider ProvideStrings
     */
    public function testThatSettingTimezoneWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setTimezone($cfp, ['timezone' => 'Europe/Berlin']);

        self::assertEquals(new DateTimeZone('Europe/Berlin'), $cfp->getTimezone());
    }

    public function testThatSettingTimezoneFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();

        self::expectException(InvalidArgumentException::class);
        CfpFactory::setTimezone($cfp, []);
    }

    /**
     * @expectedException \Exception
     */
    public function testThatSettingTimezoneFailsWithInvalidArrayEntry()
    {
        $cfp = new Cfp();

        self::expectException(Exception::class);
        CfpFactory::setTimezone($cfp, ['timezone' => 'foo']);

        self::assertEquals(new DateTimeZone('Europe/Berlin'), $cfp->getTimezone());
    }

    /**
     * @dataProvider ProvideDateTime
     */
    public function testThatSettingDateCfpStartWorksWithCorrectDateString($value)
    {
        $cfp = new Cfp();
        CfpFactory::setDateCfpStart($cfp, ['dateCfpStart' => $value]);

        self::assertEquals(new DateTimeImmutable($value), $cfp->getDateCfpStart());
    }

    public function testThatSettingDateCfpStartWorksEvenWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setDateCfpStart($cfp, []);

        self::assertEquals(new DateTimeImmutable('@0'), $cfp->getDateCfpStart());
    }

    /**
     * @dataProvider ProvideDateTime
     */
    public function testThatSettingDateCfpEndWorksWithCorrectDateString($value)
    {
        $cfp = new Cfp();
        CfpFactory::setDateCfpEnd($cfp, ['dateCfpEnd' => $value]);

        self::assertEquals(new DateTimeImmutable($value), $cfp->getDateCfpEnd());
    }

    public function testThatSettingDateCfpEndFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        self::expectException(InvalidArgumentException::class);
        CfpFactory::setDateCfpEnd($cfp, []);
    }

    /**
     * @dataProvider ProvideDateTime
     */
    public function testThatSettingDateEventStartWorksWithCorrectDateString($value)
    {
        $cfp = new Cfp();
        CfpFactory::setDateEventStart($cfp, ['dateEventStart' => $value]);

        self::assertEquals(new DateTimeImmutable($value), $cfp->getDateEventStart());
    }

    public function testThatSettingDateEventStartWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setDateEventStart($cfp, []);

        self::assertEquals(new DateTimeImmutable('0000-00-00 00:00:00+00:00'), $cfp->getDateEventStart());
    }

    /**
     * @dataProvider ProvideDateTime
     */
    public function testThatSettingDateEventEndWorksWithCorrectDateString($value)
    {
        $cfp = new Cfp();
        CfpFactory::setDateEventEnd($cfp, ['dateEventEnd' => $value]);

        self::assertEquals(new DateTimeImmutable($value), $cfp->getDateEventEnd());
    }

    public function testThatSettingDateEventEndWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setDateEventEnd($cfp, []);

        self::assertEquals(new DateTimeImmutable('0000-00-00 00:00:00+00:00'), $cfp->getDateEventEnd());
    }

    /**
     * @dataProvider ProvideUri
     */
    public function testThatSettingUriWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setUri($cfp, ['uri' => $value]);

        self::assertEquals($expected, $cfp->getUri());
    }

    public function testThatSettingUriFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();

        self::expectException(InvalidArgumentException::class);
        CfpFactory::setUri($cfp, []);
    }

    /**
     * @dataProvider ProvideSanitizedUri
     */
    public function testThatSettingEventUriWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setEventUri($cfp, ['eventUri' => $value], new Client());

        self::assertEquals($expected, $cfp->getEventUri());
    }

    public function testThatSettingIconUriWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setIconUri($cfp, []);

        self::assertEquals('', $cfp->getIconUri());
    }

    /**
     * @dataProvider ProvideUri
     */
    public function testThatSettingIconUriWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setIconUri($cfp, ['iconUri' => $value]);

        self::assertEquals($expected, $cfp->getIconUri());
    }

    public function testThatSettingEventUriFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        self::expectException(InvalidArgumentException::class);

        CfpFactory::setEventUri($cfp, [], $this->createMock(Client::class));
    }


    /**
     * @dataProvider ProvideArray
     */
    public function testThatSettingTagsWorksWithCorrectArray($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setTags($cfp, ['tags' => $value]);

        self::assertEquals($expected, $cfp->getTags());
    }

    public function testThatSettingTagsWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setTags($cfp, []);

        self::assertEquals([], $cfp->getTags());
    }

    public function provideStrings()
    {
        return [
            ['foo', 'foo'],
            ['foo', 'foo<script></script>'],
        ];
    }

    public function provideArray()
    {
        return [
            [['foo', 'bar'],['foo', 'bar']],
            [['foo', 'bar'],['foo<script></script>', 'bar']],
        ];
    }

    public function provideDateTime()
    {
        return [
            ['2016-12-13 12:23:34+02:00'],
        ];
    }

    public function provideUri()
    {
        return [
            ['http://example.com/', 'http://example.com/'],
            ['', 'grummel'],
        ];
    }

    public function provideSanitizedUri()
    {
        return [
            ['http://example.com', 'http://example.com'],
            ['https://www.wdv.de', 'http://wdv.de'],
            ['http://example.com', 'http://example.com?test'],
            ['http://example.com', 'http://example.com/?test'],
        ];
    }

    public function testThatSettingGeolocationWithoutLatitudeFails()
    {
        $cfp = new Cfp();
        CfpFactory::setGeolocation($cfp, ['longitude' => '']);

        self::assertEquals(0.0, $cfp->getLongitude());
        self::assertEquals(0.0, $cfp->getLatitude());
    }

    public function testThatSettingGeolocationWithoutLongitudeFails()
    {
        $cfp = new Cfp();
        CfpFactory::setGeolocation($cfp, ['latitude' => '']);

        self::assertEquals(0.0, $cfp->getLongitude());
        self::assertEquals(0.0, $cfp->getLatitude());
    }

    /**
     * @dataProvider provideFaultyGeolocations
     */
    public function testThatSettingGeolocationFailsWithValuesOutOfRange($lat, $lon)
    {
        $cfp = new Cfp();
        self::expectException(UnexpectedValueException::class);
        CfpFactory::setGeolocation($cfp, [
            'latitude' => $lat,
            'longitude' => $lon,
        ]);
    }


    /**
     * @dataProvider provideGeolocations
     */
    public function testThatSettingGeolocationWorksWithDifferentData($expectedLat, $expectedLon, $lat, $lon)
    {
        $cfp = new Cfp();
        CfpFactory::setGeolocation($cfp, [
            'latitude' => $lat,
            'longitude' => $lon,
        ]);
        self::assertEquals($expectedLon, $cfp->getLongitude());
        self::assertEquals($expectedLat, $cfp->getLatitude());
    }

    public function provideGeolocations()
    {
        return [
            [1.0, 1.0, '1.0', '1.0'],
            [0.0, 10.0, 'foo', '1,0'],
        ];
    }

    public function provideFaultyGeolocations()
    {
        return [
            [90.01, 1.0],
            [-90.01, 0.0],
            [1.0, 180.01],
            [1.0, -180.01],
        ];
    }
}
