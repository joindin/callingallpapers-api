<?php

namespace CallingallpapersTest\Api\Entity;

use Callingallpapers\Api\Entity\Cfp;
use PHPUnit\Framework\TestCase;

final class CfpTest extends TestCase
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

        self::assertEquals([], $cfp->getSource());

        $cfp->addSource('foo');

        self::assertEquals(['foo'], $cfp->getSource());

        $cfp->addSource('bar');

        self::assertEquals(['foo', 'bar'], $cfp->getSource());

        $cfp->addSource('foo');

        self::assertEquals(['foo', 'bar'], $cfp->getSource());
    }
}
