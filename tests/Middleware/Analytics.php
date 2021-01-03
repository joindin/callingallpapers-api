<?php

declare(strict_types=1);

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Api\Middleware;

use TheIconic\Tracking\GoogleAnalytics\Analytics as Base;

class Analytics extends Base
{
    public function sendEvent() : void
    {
    }
}
