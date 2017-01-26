<?php
/**
 * Copyright (c) 2016-2016 The callingallpapers.com Developer Team
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
 * @copyright 2016-2016 The callingallpapers.com Developer Team
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     16.01.2016
 * @link      https://github.com/joindin/callingallpapers-api
 */

namespace Callingallpapers\Api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TheIconic\Tracking\GoogleAnalytics\Analytics;

class NotifyGoogleAnalytics
{
    private $analytics;

    public function __construct(Analytics $analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * Convert a GET-Param "type" to an Accept-Header
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $response = $next($request, $response);

        $this->analytics
             ->setEventCategory($request->getUri()->getPath())
             ->setEventAction($request->getMethod())
             ->setEventLabel('type')
             ->setEventValue($response->getHeader('Content-Type')[0])
             ->setIpOverride($request->getAttribute('ip_address'))
             ->setUserAgentOverride($request->getHeader('User-Agent')[0])
             ->sendEvent();

        return $response;
    }
}
