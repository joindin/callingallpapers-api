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
        $cfp->setName(filter_var($params['name'], FILTER_SANITIZE_STRING));
        $cfp->setDateCfpStart(new DateTimeImmutable($params['dateCfpStart']));
        $cfp->setDateCfpEnd(new DateTimeImmutable($params['dateCfpEnd']));
        $cfp->setTimezone(filter_var($params['timezone'], FILTER_SANITIZE_STRING));
        $cfp->setUri(filter_var($params['uri'], FILTER_VALIDATE_URL));
        $cfp->setEventUri(filter_var($params['eventUri'], FILTER_VALIDATE_URL));

        if (isset($params['dateEventStart'])) {
            $cfp->setDateEventStart(new DateTimeImmutable($params['dateEventStart']));
        } else {
            $cfp->setDateEventStart(new DateTimeImmutable('0000-00-00 00:00:00+00:00'));
        }
        if (isset($params['dateEventEnd'])) {
            $cfp->setDateEventEnd(new DateTimeImmutable($params['dateEventEnd']));
        } else {
            $cfp->setDateEventEnd(new DateTimeImmutable('0000-00-00 00:00:00+00:00'));
        }
        if (isset($params['iconUri'])) {
            $cfp->setIconUri(filter_var($params['iconUri'],
                FILTER_VALIDATE_URL));
        }
        if (isset($params['description'])) {
            $cfp->setDescription(filter_var($params['description'],
                FILTER_SANITIZE_STRING));
        }
        if (isset($params['location'])) {
            $cfp->setLocation(filter_var($params['location'],
                FILTER_SANITIZE_STRING));
        }
        if (isset($params['latitude']) && isset($params['longitude'])) {
            $latitude  = filter_var($params['latitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $longitude = filter_var($params['longitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            if ($latitude > 90 || $latitude < -90) {
                throw new \UnexpectedValueException(sprintf(
                    'latitude has to be within a range of -90.0 to 90.0 bus is %1$f',
                    $latitude
                ), 400);
            }

            if ($longitude > 180 || $longitude < -180) {
                throw new \UnexpectedValueException(sprintf(
                    'longitude has to be within a range of -180.0 to 180.0 but is %1$f',
                    $longitude
                ), 400);
            }

            // TODO: Rewrite lat and long to be in the correct range

            $cfp->setLatitude($latitude);
            $cfp->setLongitude($longitude);
        }

        if (isset($params['tags'])) {
            $cfp->setTags(array_filter($params['tags'], function ($item) {
                return filter_var($item, FILTER_SANITIZE_STRING);
            }));
        }

        return $cfp;
    }
}