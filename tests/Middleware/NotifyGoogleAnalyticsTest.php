<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
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
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     29.12.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace CallingallpapersTest\Api\Middleware;

use Callingallpapers\Api\Middleware\NotifyGoogleAnalytics;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use TheIconic\Tracking\GoogleAnalytics\Analytics;
use Mockery as M;

class NotifyGoogleAnalyticsTest extends TestCase
{
    public function testThatInstantiationWorksAsExpected()
    {
        $ga = $this->getMockBuilder(Analytics::class)->disableOriginalConstructor()->getMock();
        $inst = new NotifyGoogleAnalytics($ga);

        $this->assertInstanceOf(NotifyGoogleAnalytics::class, $inst);
        $this->assertAttributeSame($ga, 'analytics', $inst);
    }

    public function testThatGAisNotified()
    {
        $request = M::mock(ServerRequestInterface::class);
        $response = M::mock(ResponseInterface::class);

        $uri = M::mock(UriInterface::class);
        $uri->shouldReceive('getPath')->once()->andReturn('path');

        $request->shouldReceive('getUri')->once()->andReturn($uri);
        $request->shouldReceive('getMethod')->once()->andREturn('action');

        $response->shouldReceive('getHeader')->once()->with('Content-Type')->andReturn(['value']);
        $request->shouldReceive('getAttribute')->once()->with('ip_address')->andReturn('ip');
        $request->shouldReceive('getHeader')->once()->with('User-Agent')->andReturn(['agent']);

        $callable = function (ServerRequestInterface $request, ResponseInterface $response) {
            return $response;
        };

        $ga = M::mock(Analytics::class);
        $ga->shouldReceive('setEventCategory')->once()->with('path')->andReturn($ga);
        $ga->shouldReceive('setEventAction')->once()->with('action')->andReturn($ga);
        $ga->shouldReceive('setEventLabel')->once()->with('type')->andReturn($ga);
        $ga->shouldReceive('setEventValue')->once()->with('value')->andReturn($ga);
        $ga->shouldReceive('setIpOverride')->once()->with('ip')->andReturn($ga);
        $ga->shouldReceive('setUserAgentOverride')->once()->with('agent')->andReturn($ga);
        $ga->shouldReceive('sendEvent');

        $inst = new NotifyGoogleAnalytics($ga);

        $this->assertSame($response, $inst($request, $response, $callable));
    }
}
