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

namespace Callingallpapers\Api\Middleware;

use Callingallpapers\Api\Renderer\IcalendarRenderer;
use Callingallpapers\Api\Renderer\RssRenderer;
use Callingallpapers\Api\Renderer\TwigRenderer;
use JsonHelpers\JsonHelpers;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Renderer
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    /**
     * Example middleware invokable class
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
        $accept = $request->getHeader('Accept');

        if (strpos($accept[0], 'text/calendar') !== false) {
            $container         = $this->app->getContainer();
            $container['view'] = new IcalendarRenderer();
        } elseif (strpos($accept[0], 'application/rss+xml') !== false) {
            $container         = $this->app->getContainer();
            $container['view'] = new RssRenderer();
        } elseif (strpos($accept[0], 'text/html') !== false) {
            $container         = $this->app->getContainer();
            $container['view'] = function ($container) {
                $config = $container['settings'];
                $tView  = new \Slim\Views\Twig(
                    $config['renderer']['template_path'],
                    [
                        'cache' => $config['renderer']['cache_path'],
                    ]
                );
                $tView->addExtension(new \Slim\Views\TwigExtension(
                    $container['router'],
                    $container['request']->getUri()
                ));

                $view = new TwigRenderer($tView);

                return $view;
            };
        } else {
            $jsonHelpers = new JsonHelpers($this->app->getContainer());
            $jsonHelpers->registerResponseView();
            $jsonHelpers->registerErrorHandlers();
        }

        return $next($request, $response);
    }
}
