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
use Mockery as M;

class CfpFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider ProvideStrings
     */
    public function testThatSettingNameWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setName($cfp, ['name' => $value]);

        $this->assertAttributeEquals($expected, 'name', $cfp);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThatSettingNameFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setName($cfp, []);
    }

    /**
     * @dataProvider ProvideStrings
     */
    public function testThatSettingDescriptionWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setDescription($cfp, ['description' => $value]);

        $this->assertAttributeEquals($expected, 'description', $cfp);
    }

    public function testThatSettingDescriptionWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setDescription($cfp, []);

        $this->assertAttributeEquals('', 'description', $cfp);
    }

    /**
     * @dataProvider ProvideStrings
     */
    public function testThatSettingLocationWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setLocation($cfp, ['location' => $value]);

        $this->assertAttributeEquals($expected, 'location', $cfp);
    }

    public function testThatSettingLocationWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setLocation($cfp, []);

        $this->assertAttributeEquals('', 'location', $cfp);
    }

    /**
     * @dataProvider ProvideStrings
     */
    public function testThatSettingTimezoneWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setTimezone($cfp, ['timezone' => 'Europe/Berlin']);

        $this->assertAttributeEquals(new \DateTimeZone('Europe/Berlin'), 'timezone', $cfp);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThatSettingTimezoneFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setTimezone($cfp, []);
    }

    /**
     * @expectedException \Exception
     */
    public function testThatSettingTimezoneFailsWithInvalidArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setTimezone($cfp, ['timezone' => 'foo']);

        $this->assertAttributeEquals(new \DateTimeZone('Europe/Berlin'), 'timezone', $cfp);
    }

    /**
     * @dataProvider ProvideDateTime
     */
    public function testThatSettingDateCfpStartWorksWithCorrectDateString($value)
    {
        $cfp = new Cfp();
        CfpFactory::setDateCfpStart($cfp, ['dateCfpStart' => $value]);

        $this->assertAttributeEquals(new \DateTimeImmutable($value), 'dateCfpStart', $cfp);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThatSettingDateCfpStartFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setDateCfpStart($cfp, []);
    }

    /**
     * @dataProvider ProvideDateTime
     */
    public function testThatSettingDateCfpEndWorksWithCorrectDateString($value)
    {
        $cfp = new Cfp();
        CfpFactory::setDateCfpEnd($cfp, ['dateCfpEnd' => $value]);

        $this->assertAttributeEquals(new \DateTimeImmutable($value), 'dateCfpEnd', $cfp);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThatSettingDateCfpEndFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setDateCfpEnd($cfp, []);
    }

    /**
     * @dataProvider ProvideDateTime
     */
    public function testThatSettingDateEventStartWorksWithCorrectDateString($value)
    {
        $cfp = new Cfp();
        CfpFactory::setDateEventStart($cfp, ['dateEventStart' => $value]);

        $this->assertAttributeEquals(new \DateTimeImmutable($value), 'dateEventStart', $cfp);
    }

    public function testThatSettingDateEventStartWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setDateEventStart($cfp, []);

        $this->assertAttributeEquals(new \DateTimeImmutable('0000-00-00 00:00:00+00:00'), 'dateEventStart', $cfp);
    }

    /**
     * @dataProvider ProvideDateTime
     */
    public function testThatSettingDateEventEndWorksWithCorrectDateString($value)
    {
        $cfp = new Cfp();
        CfpFactory::setDateEventEnd($cfp, ['dateEventEnd' => $value]);

        $this->assertAttributeEquals(new \DateTimeImmutable($value), 'dateEventEnd', $cfp);
    }

    public function testThatSettingDateEventEndWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setDateEventEnd($cfp, []);

        $this->assertAttributeEquals(new \DateTimeImmutable('0000-00-00 00:00:00+00:00'), 'dateEventEnd', $cfp);
    }

    /**
     * @dataProvider ProvideUri
     */
    public function testThatSettingUriWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setUri($cfp, ['uri' => $value]);

        $this->assertAttributeEquals($expected, 'uri', $cfp);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThatSettingUriFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setUri($cfp, []);
    }

    /**
     * @dataProvider ProvideUri
     */
    public function testThatSettingEventUriWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setEventUri($cfp, ['eventUri' => $value]);

        $this->assertAttributeEquals($expected, 'eventUri', $cfp);
    }

    public function testThatSettingIconUriWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setIconUri($cfp, []);

        $this->assertAttributeEquals('', 'iconUri', $cfp);
    }

    /**
     * @dataProvider ProvideUri
     */
    public function testThatSettingIconUriWorksWithCorrectString($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setIconUri($cfp, ['iconUri' => $value]);

        $this->assertAttributeEquals($expected, 'iconUri', $cfp);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThatSettingEventUriFailsWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setEventUri($cfp, []);
    }


    /**
     * @dataProvider ProvideArray
     */
    public function testThatSettingTagsWorksWithCorrectArray($expected, $value)
    {
        $cfp = new Cfp();
        CfpFactory::setTags($cfp, ['tags' => $value]);

        $this->assertAttributeEquals($expected, 'tags', $cfp);
    }

    public function testThatSettingTagsWorksWithMissingArrayEntry()
    {
        $cfp = new Cfp();
        CfpFactory::setTags($cfp, []);

        $this->assertAttributeEquals([], 'tags', $cfp);
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
            ['http://example.com', 'http://example.com'],
            ['', 'grummel'],
        ];
    }

    public function testThatSettingGeolocationWithoutLatitudeFails()
    {
        $cfp = new Cfp();
        CfpFactory::setGeolocation($cfp, ['longitude' => '']);
        $this->assertAttributeEquals(0.0, 'longitude', $cfp);
        $this->assertAttributeEquals(0.0, 'latitude', $cfp);
    }

    public function testThatSettingGeolocationWithoutLongitudeFails()
    {
        $cfp = new Cfp();
        CfpFactory::setGeolocation($cfp, ['latitude' => '']);
        $this->assertAttributeEquals(0.0, 'longitude', $cfp);
        $this->assertAttributeEquals(0.0, 'latitude', $cfp);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @dataProvider provideFaultyGeolocations
     */
    public function testThatSettingGeolocationFailsWithValuesOutOfRange($lat, $lon)
    {
        $cfp = new Cfp();
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
        $this->assertAttributeEquals($expectedLon, 'longitude', $cfp);
        $this->assertAttributeEquals($expectedLat, 'latitude', $cfp);
    }

    public function provideGeolocations()
    {
        return [
            [1.0, 1.0, '1.0', '1.0'],
            ['', 10.0, 'foo', '1,0'],
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
