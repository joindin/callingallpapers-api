<?php

namespace CallingallpapersTest\Api\Entity;

use Callingallpapers\Api\Entity\Cfp;

final class CfpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setEventUriTrimsTrailingSlash()
    {
        $uri = 'https://some/uri';

        $cfp = new Cfp();
        $cfp->setEventUri($uri . '/');

        self::assertEquals($uri, $cfp->getEventUri());
    }

    public function testThatAddingASourceActuallyWorks()
    {
        $cfp = new Cfp();

        self::assertAttributeEquals([], 'source', $cfp);

        $cfp->addSource('foo');

        self::assertAttributeEquals(['foo'], 'source', $cfp);

        $cfp->addSource('bar');

        self::assertAttributeEquals(['foo', 'bar'], 'source', $cfp);

        $cfp->addSource('foo');

        self::assertAttributeEquals(['foo', 'bar'], 'source', $cfp);

    }
}
