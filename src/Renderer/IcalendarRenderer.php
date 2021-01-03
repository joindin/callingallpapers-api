<?php
/**
 * Copyright (c) 2016-2016} Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
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
namespace Callingallpapers\Api\Renderer;

use Psr\Http\Message\ResponseInterface;
use Sabre\VObject\Component\VCalendar;

class IcalendarRenderer
{
    public function render(ResponseInterface $response, array $data = [], int $status = 200): ResponseInterface
    {
        $icalendar = new VCalendar();
        if (! isset($data['cfps'])) {
            $data['cfps'] = [];
        }
        foreach ($data['cfps'] as $cfp) {
            $cfpStart = new \DateTime($cfp['dateCfpStart']);
            $cfpEnd   = new \DateTime($cfp['dateCfpEnd']);
            $lastChange = new \DateTime($cfp['lastChange']);
            $lastChange->setTimezone(new \DateTimeZone('UTC'));
            if ($cfp['timezone']) {
                $cfpStart->setTimezone(new \DateTimeZone($cfp['timezone']));
                $cfpEnd->setTimezone(new \DateTimeZone($cfp['timezone']));
            }

            $icalendar->add('VEVENT', [
                'SUMMARY' => $cfp['name'],
                'DTSTART' => $cfpStart,
                'DTEND'   => $cfpEnd,
                'URL'     => $cfp['uri'],
                'DTSTAMP' => $lastChange,
                'UID'     => $cfp['_rel']['cfp_uri'],
                'DESCRIPTION' => $cfp['description'],
                'GEO'     => round($cfp['latitude'], 6) . ';' . round($cfp['longitude'], 6),
                'LOCATION' => $cfp['location'],

            ]);
        }

        $response = $response->withHeader('Content-Type', 'text/calendar');
        $response = $response->withStatus($status);

        $stream = $response->getBody();
        $stream->write($icalendar->serialize());

        return $response->withBody($stream);
    }
}
