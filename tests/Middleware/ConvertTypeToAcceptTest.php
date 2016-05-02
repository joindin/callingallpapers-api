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
 * @since     01.05.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace CallingallpapersTest\Api\Middleware;

use Callingallpapers\Api\Middleware\ConvertTypeToAccept;

class ConvertTypeToAcceptTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider responseIsUnchangedWithoutTypeParameterProvider
     */
    public function testThatResponseIsUnchangedWithoutTypeParameter($queryString)
    {
        // instantiate action
        $action = new ConvertTypeToAccept();

        // We need a request and response object to invoke the action
        $environment = \Slim\Http\Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/echo',
                'QUERY_STRING'=>$queryString]
        );
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();

        // run the controller action and test it
        $newRequest = $action($request, $response, function ($request, $response) {
            return $request;
        });

        $this->assertSame($request, $newRequest);
    }

    public function responseIsUnchangedWithoutTypeParameterProvider()
    {
        return [
            [''],
            ['foo=bar'],
            ['type=foo'],
        ];
    }

    /**
     * @dataProvider acceptIsSetWithPropertGetParameterProvider
     */
    public function testThatAcceptIsSetWithProperGetParameter($parameter, $acceptHeader)
    {
        // instantiate action
        $action = new ConvertTypeToAccept();

        // We need a request and response object to invoke the action
        $environment = \Slim\Http\Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/echo',
                'QUERY_STRING'=>'type=' . $parameter]
        );
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();

        // run the controller action and test it
        $newRequest = $action($request, $response, function ($request, $response) {
            return $request;
        });

        $this->assertEquals([$acceptHeader], $newRequest->getHeader('Accept'));
    }

    public function acceptIsSetWithPropertGetParameterProvider()
    {
        return [
            ['calendar', 'text/calendar'],
            ['rss', 'application/rss+xml'],
        ];
    }
}
