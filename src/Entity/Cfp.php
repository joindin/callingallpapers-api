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
 * @since     16.01.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace Callingallpapers\Api\Entity;

class Cfp
{
    /**
     * @var string The URI for the CfP. This is the unique identifier for
     *             different CfPs.
     */
    protected $uri = '';

    /**
     * @var string The name of the event this CfP belongs to
     */
    protected $name = '';

    /**
     * @var \DateTimeInterface The start-date of the CfP
     */
    protected $dateCfpStart;

    /**
     * @var \DateTimeInterface The end date of the CfP
     */
    protected $dateCfpEnd;

    /**
     * @var string The Name of the venue
     */
    protected $location;

    /**
     * @var float The latitude of the venue
     */
    protected $latitude;

    /**
     * @var float The longitude of the venue
     */
    protected $longitude;

    /**
     * @var string The description of the Event
     */
    protected $description;

    /**
     * @var \DateTimeImmutable The date the event starts at
     */
    protected $dateEventStart;

    /**
     * @var \DateTimeImmutable The date the event ends at
     */
    protected $dateEventEnd;

    /**
     * @var string the URI of an icon of the Event
     */
    protected $iconUri;

    /**
     * @var string The URI of the events-site
     */
    protected $eventUri;

    /**
     * @var \DateTimeZone The Timezone of the CFP-Dates
     */
    protected $timezone;

    /**
     * @var array the tags of the CfP resp. the Event
     */
    protected $tags = [];

    /**
     * @var \DateTimeInterface Date of the last change
     */
    protected $lastUpdate = null;

    /**
     * @var array The sources of a CfP
     */
    protected $source = [];

    public function __construct()
    {
        $this->dateCfpStart   = new \DateTimeImmutable('@0');
        $this->dateCfpEnd     = new \DateTimeImmutable();
        $this->dateEventStart = new \DateTimeImmutable();
        $this->dateEventEnd   = new \DateTimeImmutable();
        $this->timezone       = new \DateTimezone('UTC');
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDateCfpStart(\DateTimeInterface $startDate)
    {
        $this->dateCfpStart = $startDate;
    }

    public function getDateCfpStart()
    {
        return $this->dateCfpStart;
    }

    public function setDateCfpEnd(\DateTimeInterface $endDate)
    {
        $this->dateCfpEnd = $endDate;
    }

    public function getDateCfpEnd()
    {
        return $this->dateCfpEnd;
    }

    public function getId()
    {
        return $this->getHash();
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDateEventStart(\DateTimeInterface $startDate)
    {
        $this->dateEventStart = $startDate;
    }

    public function getDateEventStart()
    {
        return $this->dateEventStart;
    }

    public function setDateEventEnd(\DateTimeInterface $endDate)
    {
        $this->dateEventEnd = $endDate;
    }

    public function getDateEventEnd()
    {
        return $this->dateEventEnd;
    }

    public function setIconUri($iconUri)
    {
        $this->iconUri = $iconUri;
    }

    public function getIconUri()
    {
        return $this->iconUri;
    }

    public function setEventUri($eventUri)
    {
        $this->eventUri = rtrim($eventUri, '/');
    }

    public function getEventUri()
    {
        return $this->eventUri;
    }

    public function setTimezone($timezone)
    {
        if (! $timezone instanceof \DateTimeZone) {
            $timezone = new \DateTimeZone($timezone);
        }

        $this->timezone = $timezone;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function getHash()
    {
        return sha1($this->getEventUri());
    }

    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getLastUdated()
    {
        return $this->lastUpdate;
    }

    public function setLastUpdated(\DateTimeinterface $date)
    {
        $this->lastUpdate = $date;
    }

    public function setSource(array $source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function addSource($source)
    {
        if (! in_array($source, $this->source)) {
            $this->source[] = $source;
        }
    }
}
