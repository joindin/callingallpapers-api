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
use Zend\Feed\Writer\Feed;

class RssRenderer
{
    public function render(ResponseInterface $response, array $data = [], int $status = 200): ResponseInterface
    {
        $feed = new Feed();
        $feed->setTitle('Calling all Papers');
        $feed->setDescription('Calls for Papers that are currently open');
        $feed->setLink('http://callingallpapers.com');
        $feed->setFeedLink('http://api.callingallpapers.com/v1/cfp', 'rss');
        $feed->setFeedLink('http://api.callingallpapers.com/v1/cfp', 'atom');
        $feed->addAuthor([
            'name' => 'Andreas Heigl',
            'email' => 'andreas@heigl.org',
            'uri'   => 'http://andreas.heigl.org',
        ]);
        $feed->setDateModified(time());
        if (! isset($data['cfps'])) {
            $data['cfps'] = [];
        }
        foreach ($data['cfps'] as $cfp) {
            try {
                $lastChange = new \DateTime($cfp['lastChange']);
                $lastChange->setTimezone(new \DateTimeZone('UTC'));

                $entry = $feed->createEntry();
                $entry->setTitle($cfp['name']);
                $entry->setLink($cfp['uri']);
                $entry->setDateModified(new \DateTime($cfp['dateCfpEnd']));
                $entry->setDateCreated(new \DateTime($cfp['dateCfpEnd']));
                $entry->setDescription(sprintf(
                    'CfP for %3$s runs from %1$s to %2$s. The event runs from %4$s to %5$s in %6$s',
                    (new \DateTime($cfp['dateCfpStart']))->format('c'),
                    (new \DateTime($cfp['dateCfpEnd']))->format('c'),
                    $cfp['name'],
                    (new \DateTime($cfp['dateEventStart']))->format('c'),
                    (new \DateTime($cfp['dateEventEnd']))->format('c'),
                    $cfp['location']
                ));
                $entry->setContent($cfp['description']);
                $entry->setId($cfp['eventUri']);

                $feed->addEntry($entry);
            } catch (\Exception $e) {
            }
        }

        $response = $response->withHeader('Content-Type', 'application/rss+xml');
        $response = $response->withStatus($status);

        $stream = $response->getBody();
        $stream->write($feed->export('rss'));

        return $response->withBody($stream);
    }
}
