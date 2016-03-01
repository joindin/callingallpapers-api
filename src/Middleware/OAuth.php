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
 * @link      https://github.com/joindin/callingallpapers
 */

namespace Callingallpapers\Api\Middleware;

use Callingallpapers\Api\PersistenceLayer\UserPersistenceLayer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OAuth
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
        $auth = $request->getHeader('Authenticate');
        if ($request->getMethod() === 'GET') {
            // Get is allowed without authentication
            // Rate-Limit is handlede by another Middleware
            return $next($request, $response);
        }
        if (! $auth) {
            $response = $response->withHeader('WWW-Authenticate', 'Bearer realm="callingallpapers", error="no token", error_desciption="No Access-Token provided"');
            $response = $response->withStatus(401);

            return $response;
        }

        $bearer = explode(' ', $auth[0]);
        if (! isset($bearer[1])) {
            $response = $response->withHeader('WWW-Authenticate', 'Bearer realm="callingallpapers", error="no token", error_desciption="No Access-Token provided"');
            $response = $response->withStatus(401);

            return $response;
        }
        $bearer = $bearer[1];

        $upl = new UserPersistenceLayer($this->app->getContainer()['pdo']);
        try {
            $user = $upl->getUserForToken($bearer);
        } catch (\Exception $e) {
            $response = $response->withHeader('WWW-Authenticate', 'Bearer realm="callingallpapers", error="invalid token", error_desciption="Invalid Access-Token provided"');
            $response = $response->withStatus(401);

            return $response;
        }

        $request = $request->withAttribute('user', $user['user']);

        return $next($request, $response);
    }
}
