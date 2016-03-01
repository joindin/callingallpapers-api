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
 * @since     18.02.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace Callingallpapers\Api\Service;

use Callingallpapers\Api\Entity\Cfp;
use DateTimeImmutable;

class CfpFactory
{

    /**
     * Create a CFP from unfiltered values.
     * This method takes an array of unfiltered values, checks for required
     * values and validates resp. sanitizes those values before injecting them
     * into a new CfP
     *
     * @param array $params
     *
     * @return Cfp
     */
    public function createCfp(array $params)
    {
        $requiredFields = ['name', 'dateCfpStart', 'dateCfpEnd', 'uri', 'eventUri', 'timezone'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (! isset($params[$field])) {
                $missingFields[] = $field;
            }
        }

        if ($missingFields) {
            throw new \UnexpectedValueException(sprintf(
                'The following fields are missing: "%1$s"',
                implode(', ', $missingFields)
            ), 400);
        }
        $cfp = new Cfp();

        self::setName($cfp, $params);
        self::setDateCfpStart($cfp, $params);
        self::setDateCfpEnd($cfp, $params);
        self::setTimezone($cfp, $params);
        self::setUri($cfp, $params);
        self::setEventUri($cfp, $params);
        self::setDateEventStart($cfp, $params);
        self::setDateEventEnd($cfp, $params);
        self::setIconUri($cfp, $params);
        self::setDescription($cfp, $params);
        self::setLocation($cfp, $params);
        self::setGeolocation($cfp, $params);
        self::setTags($cfp, $params);

        return $cfp;
    }

    public static function setName(Cfp $cfp, array $array)
    {
        if (! isset($array['name'])) {
            throw new \InvalidArgumentException('Name has to be specified');
        }
        $cfp->setName(filter_var($array['name'], FILTER_SANITIZE_STRING));
    }

    public static function setDateCfpStart(Cfp $cfp, array $array)
    {
        if (! isset($array['dateCfpStart'])) {
            throw new \InvalidArgumentException('CFP-StartDate has to be specified');
        }
        $cfp->setDateCfpStart(new DateTimeImmutable($array['dateCfpStart']));
    }

    public static function setDateCfpEnd(Cfp $cfp, array $array)
    {
        if (! isset($array['dateCfpEnd'])) {
            throw new \InvalidArgumentException('CFP-EndDate has to be specified');
        }
        $cfp->setDateCfpEnd(new DateTimeImmutable($array['dateCfpEnd']));
    }

    public static function setTimezone(Cfp $cfp, array $array)
    {
        if (! isset($array['timezone'])) {
            throw new \InvalidArgumentException('Timezone has to be specified');
        }
        $cfp->setTimezone(filter_var($array['timezone'], FILTER_SANITIZE_STRING));
    }

    public static function setUri(Cfp $cfp, array $array)
    {
        if (! isset($array['uri'])) {
            throw new \InvalidArgumentException('URI has to be specified');
        }

        $cfp->setUri(filter_var($array['uri'], FILTER_VALIDATE_URL));
    }

    public static function setEventUri(Cfp $cfp, array $array)
    {
        if (! isset($array['eventUri'])) {
            throw new \InvalidArgumentException('Event-URI has to be specified');
        }

        $cfp->setEventUri(filter_var($array['eventUri'], FILTER_VALIDATE_URL));
    }

    public static function setDateEventStart(Cfp $cfp, array $array)
    {
        if (isset($array['dateEventStart'])) {
            $cfp->setDateEventStart(new DateTimeImmutable($array['dateEventStart']));
        } else {
            $cfp->setDateEventStart(new DateTimeImmutable('0000-00-00 00:00:00+00:00'));
        }
    }

    public static function setDateEventEnd(Cfp $cfp, array $array)
    {
        if (isset($array['dateEventEnd'])) {
            $cfp->setDateEventEnd(new DateTimeImmutable($array['dateEventEnd']));
        } else {
            $cfp->setDateEventEnd(new DateTimeImmutable('0000-00-00 00:00:00+00:00'));
        }
    }

    public static function setIconUri(Cfp $cfp, array $array)
    {
        if (! isset($array['iconUri'])) {
            return;
        }

        $cfp->setIconUri(filter_var($array['iconUri'], FILTER_VALIDATE_URL));
    }

    public static function setDescription(Cfp $cfp, array $array)
    {
        if (! isset($array['description'])) {
            return;
        }

        $cfp->setDescription(filter_var($array['description'], FILTER_SANITIZE_STRING));
    }

    public static function setLocation(Cfp $cfp, array $array)
    {
        if (! isset($array['location'])) {
            return;
        }

        $cfp->setLocation(filter_var($array['location'], FILTER_SANITIZE_STRING));
    }

    public static function setGeolocation(Cfp $cfp, array $array)
    {
        if (! isset($array['latitude'])) {
            return;
        }

        if (! isset($array['longitude'])) {
            return;
        }

        $latitude  = filter_var($array['latitude'],
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION);
        $longitude = filter_var($array['longitude'],
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION);

        if ($latitude > 90 || $latitude < - 90) {
            throw new \UnexpectedValueException(sprintf(
                'latitude has to be within a range of -90.0 to 90.0 bus is %1$f',
                $latitude
            ), 400);
        }

        if ($longitude > 180 || $longitude < - 180) {
            throw new \UnexpectedValueException(sprintf(
                'longitude has to be within a range of -180.0 to 180.0 but is %1$f',
                $longitude
            ), 400);
        }

        // TODO: Rewrite lat and long to be in the correct range

        $cfp->setLatitude($latitude);
        $cfp->setLongitude($longitude);
    }

    public static function setTags(Cfp $cfp, array $array)
    {
        if (! isset($array['tags'])) {
            return;
        }

        $cfp->setTags(array_map(function ($item) {
            return filter_var($item, FILTER_SANITIZE_STRING);
        }, $array['tags']));
    }
}
