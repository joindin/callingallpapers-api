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

namespace CallingallpapersTest\Api\PersistenceLayer;

use Callingallpapers\Api\Entity\Cfp;
use Callingallpapers\Api\Entity\CfpList;
use Callingallpapers\Api\PersistenceLayer\CfpPersistenceLayer;
use Org_Heigl\PdoTimezoneHelper\Handler\PdoTimezoneHandlerInterface;
use Org_Heigl\PdoTimezoneHelper\PdoTimezoneHelper;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\Framework\TestCase;

class CfpPersistenceLayerTest extends TestCase
{
    use TestCaseTrait;

    private $conn = null;

    private static $pdo = null;

    private $timezoneHelper = null;

    public function __construct()
    {
        $this->timezoneHelper = $this->getMockBuilder(PdoTimezoneHandlerInterface::class)->getMock();
        parent::__construct();
    }

    public function getConnection()
    {
        if (null === $this->conn) {
            if (null === self::$pdo) {
                self::$pdo = new \PDO(
                    $GLOBALS['DB_DSN'],
                    $GLOBALS['DB_USER'],
                    $GLOBALS['DB_PASS']
                );
                self::$pdo->exec('CREATE TABLE cfp
            (
                id INTEGER PRIMARY KEY,
    hash TEXT,
    dateCfpStart TEXT,
    dateCfpEnd TEXT,
    uri TEXT,
    name TEXT,
    timezone TEXT DEFAULT \'UTC\' NOT NULL,
    dateEventStart TEXT,
    dateEventEnd TEXT,
    description TEXT,
    eventUri TEXT,
    iconUri TEXT,
    latitude REAL,
    longitude REAL,
    location TEXT,
    tags TEXT,
    lastUpdate TEXT,
    source TEXT
);
CREATE UNIQUE INDEX cfp_hash_uindex ON cfp (hash);
');
            }
            $this->conn = $this->createDefaultDBConnection(
                self::$pdo,
                $GLOBALS['DB_NAME']
            );
        }

        return $this->conn;
    }

    /**
     * @return \PHPUnit\DBUnit\DataSet\XmlDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__ . '/_assets/cfp-seed.xml');
    }

    public function testCreation()
    {
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $this->assertInstanceof('Callingallpapers\Api\PersistenceLayer\CfpPersistenceLayer', $cpl);
    }

    public function testCreateEntry()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('cfp'), "Pre-Condition");
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $cfp = new Cfp;
        $cfp->setEventUri('http://example.com');
        $cpl->insert($cfp);
        $this->assertEquals(3, $this->getConnection()->getRowCount('cfp'), "Post-Condition");
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testCreatingAnEntryTwice()
    {
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);
        $cfp = new Cfp;
        $cfp->setEventUri('http://example.com');
        $cpl->insert($cfp);
        $cpl->insert($cfp);
    }

    public function testSelectingEntries()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('cfp'), "Pre-Condition");
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $content = $cpl->select();
        $this->assertInstanceof('Callingallpapers\Api\Entity\CfpList', $content);
        $this->assertEquals(2, $content->count());
    }

    public function testSelectingEntry()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('cfp'), "Pre-Condition");
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $content = $cpl->select('ff');
        $this->assertInstanceof('Callingallpapers\Api\Entity\CfpList', $content);
        $this->assertEquals(1, $content->count());
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testSelectingEntryWithNonExistentHash()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('cfp'), "Pre-Condition");
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $content = $cpl->select('fg');
        $this->assertInstanceof('Callingallpapers\Api\Entity\CfpList', $content);
        $this->assertEquals(0, $content->count());
    }

    public function testRemovingEntry()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('cfp'), "Pre-Condition");
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $this->assertTrue($cpl->delete('ff'));
        $this->assertEquals(1, $this->getConnection()->getRowCount('cfp'));
    }

    public function testRemovingEntryFailsWithWrongHash()
    {
        $this->assertEquals(2, $this->getConnection()->getRowCount('cfp'), "Pre-Condition");
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $cpl->delete('fa');
        $this->assertEquals(2, $this->getConnection()->getRowCount('cfp'));
    }

    public function testUpdatingEntry()
    {
        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('cfp'),
            "Pre-Condition"
        );
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $cfp = new Cfp;
        $cfp->setEventUri('http://example.com');
        $newHash = $cpl->update($cfp, 'ff');
        $this->assertEquals(sha1('http://example.com'), $newHash);
        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('cfp'),
            "Post-Condition"
        );

        $results = $cpl->select(sha1('http://example.com'));
        $this->assertEquals(1, $results->count());
    }

    public function testThatUpdatingAnEntryWhereMergeOfTagsIsNecessaryWorks()
    {
        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('cfp'),
            "Pre-Condition"
        );
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $cfp = new Cfp;
        $cfp->setEventUri('http://example.com');
        $cfp->setTags(['foo', 'bar']);
        $newHash = $cpl->update($cfp, 'ff');

        $queryTable = $this->getConnection()->createQueryTable(
            'cfp',
            'SELECT tags FROM cfp WHERE hash="' . $newHash . '";'
        );
        $expectedTable = $this->createFlatXmlDataSet(__DIR__ . "/_assets/expectedTags.xml")
                              ->getTable("cfp");
        self::assertTablesEqual($expectedTable, $queryTable);
    }

    public function testThatUpdatingAnEntryWhereMergeOfSourcesIsNecessaryWorks()
    {
        $this->assertEquals(
            2,
            $this->getConnection()->getRowCount('cfp'),
            "Pre-Condition"
        );
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $cfp = new Cfp;
        $cfp->setEventUri('http://example.com');
        $cfp->setSource(['lanyrd.com']);
        $newHash = $cpl->update($cfp, 'ff');

        $queryTable = $this->getConnection()->createQueryTable(
            'cfp',
            'SELECT source FROM cfp WHERE hash="' . $newHash . '";'
        );
        $expectedTable = $this->createFlatXmlDataSet(__DIR__ . "/_assets/expectedSources_1.xml")
                              ->getTable("cfp");
        self::assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionCode 404
     */
    public function testUpdatingUnknownHashDoesNotWork()
    {
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $this->assertEquals(0, $cpl->select('fg')->count());
        $cfp = new Cfp;
        $cpl->update($cfp, 'fg');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionCode 400
     */
    public function testUpdatingWithoutHashDoesNotWork()
    {
        $cpl = new CfpPersistenceLayer(self::$pdo, $this->timezoneHelper);

        $cfp = new Cfp;
        $cpl->update($cfp, null);
    }
}
