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
use Closure;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class NotifyGoogleAnalyticsTest extends TestCase
{
    public function testThatInstantiationWorksAsExpected()
    {
        $ga = $this->getMockBuilder(Analytics::class)->disableOriginalConstructor()->getMock();
        $inst = new NotifyGoogleAnalytics($ga);

        $gaThief = Closure::bind(function (NotifyGoogleAnalytics $analytics) {
            return $analytics->analytics;
        }, null, NotifyGoogleAnalytics::class);

        $this->assertInstanceOf(NotifyGoogleAnalytics::class, $inst);
        $this->assertSame($ga, $gaThief($inst));
    }

    public function testThatGAisNotified()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn('path');

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);
        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn('action');

        $response->expects($this->once())
            ->method('getHeader')
            ->with('Content-Type')
            ->willReturn(['value']);
        $request->expects($this->once())
            ->method('getAttribute')
            ->with('ip_address')
            ->willReturn('ip');
        $request->expects($this->once())
            ->method('getHeader')
            ->with('User-Agent')
            ->willReturn(['agent']);

        $callable = function (ServerRequestInterface $request, ResponseInterface $response) {
            return $response;
        };

        $ga = new Analytics();
        $ga->setProtocolVersion(1);
        $ga->setTrackingId('trackingId');
        $ga->setClientId('clientId');

//        $ga->expects($this->once())
//            ->method('setEventCategory')
//            ->with('path')
//            ->willReturn($ga);
//        $ga->expects($this->once())
//            ->method('setEventAction')
//            ->with('action')
//            ->willReturn($ga);
//        $ga->expects($this->once())
//            ->method('setEventLabel')
//            ->with('type')
//            ->willReturn($ga);
//        $ga->expects($this->once())
//            ->method('setEventValue')
//            ->with('value')
//            ->willReturn($ga);
//        $ga->expects($this->once())
//            ->method('setIpOverride')
//            ->with('ip')
//            ->willReturn($ga);
//        $ga->expects($this->once())
//            ->method('setUserAgentOverride')
//            ->with('agent')
//            ->willReturn($ga);
//        $ga->expects($this->once())
//            ->method('sendEvent');

        $inst = new NotifyGoogleAnalytics($ga);

        $this->assertSame($response, $inst($request, $response, $callable));
        self::assertEquals('action', $ga->getEventAction());
        self::assertEquals('path', $ga->getEventCategory());
        self::assertEquals('type', $ga->getEventLabel());
        self::assertEquals('value', $ga->getEventValue());
        self::assertEquals('ip', $ga->getIpOverride());
        self::assertEquals('agent', $ga->getUserAgentOverride());
    }
}
