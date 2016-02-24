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
 * @since     19.01.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace Callingallpapers\Api\PersistenceLayer;

use Callingallpapers\Api\Entity\Cfp;
use Monolog\Logger;

class CfpPersistenceLayer
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert(Cfp $cfp)
    {

        $cfpExists = false;
        try {
            $this->select($cfp->getHash());
            $cfpExists = true;
        } catch(\UnexpectedValueException $e) {
            $cfpExists = false;
        }

        if ($cfpExists) {
            throw new \UnexpectedValueException(sprintf(
                'There is already a CFP with URL "%1$s".',
                $cfp->getEventUri()
            ), 400);
        }
        $statement = 'INSERT into `cfp`(`dateCfpStart`, `dateCfpEnd`, `dateEventStart`, `dateEventEnd`, `name`, `uri`, `hash`, `timezone`, `description`, `eventUri`, `iconUri`, `latitude`, `longitude`, `location`, `tags`, `lastUpdate`) ' .
                     'VALUES (:dateCfpStart, :dateCfpEnd, :dateEventStart, :dateEventEnd, :name, :uri, :hash, :timezone, :description, :eventUri, :iconUri, :latitude, :longitude, :location, :tags, :lastUpdate);';
        $statement = $this->pdo->prepare($statement);

        $values = [
            'dateCfpStart'   => $cfp->getDateCfpStart()->format('c'),
            'dateCfpEnd'     => $cfp->getDateCfpEnd()->format('c'),
            'dateEventStart' => $cfp->getDateEventStart()->format('c'),
            'dateEventEnd'   => $cfp->getDateEventEnd()->format('c'),
            'name'           => $cfp->getName(),
            'uri'            => $cfp->getUri(),
            'hash'           => $cfp->getHash(),
            'timezone'       => $cfp->getTimezone()->getName(),
            'description'    => $cfp->getDescription(),
            'eventUri'       => $cfp->getEventUri(),
            'iconUri'        => $cfp->getIconUri(),
            'latitude'       => $cfp->getLatitude(),
            'longitude'      => $cfp->getLongitude(),
            'location'       => $cfp->getLocation(),
            'tags'           => implode(',', $cfp->getTags()),
            'lastUpdate'     => (new \DateTime('now', new \DateTimezone('UTC')))->format('c'),
        ];


        if ($statement->execute($values)) {
            return $values['hash'];
        }

        throw new \UnexpectedValueException('The CfP could not be stored', 400);
    }

    public function update(Cfp $cfp, $fetchHash)
    {
        if (! $fetchHash) {
            throw new \UnexpectedValueException('No Hash given', 400);
        }
        if ($this->select($fetchHash)->count() != 1) {
            throw new \UnexpectedValueException('There is no CFP with this URL.', 404);
        }
        $statement = 'UPDATE `cfp` SET '
                   . '`dateCfpStart` = :dateCfpStart,'
                   . '`dateCfpEnd` = :dateCfpEnd,'
                   . '`name` = :name,'
                   . '`uri` = :uri,'
                   . '`hash` = :hash,'
                   . '`timezone` = :timezone, '
                   . '`dateEventStart` = :dateEventStart, '
                   . '`dateEventEnd` = :dateEventEnd, '
                   . '`description` = :description, '
                   . '`eventUri` = :eventUri, '
                   . '`iconUri` = :iconUri, '
                   . '`latitude` = :latitude, '
                   . '`longitude` = :longitude, '
                   . '`location` = :location, '
                   . '`tags` = :tags, '
                   . '`lastUpdate` = :lastUpdate '
                   . 'WHERE `hash` = :fetchHash';
        $statement = $this->pdo->prepare($statement);

        $values = [
            'dateCfpStart'   => $cfp->getDateCfpStart()->format('c'),
            'dateCfpEnd'     => $cfp->getDateCfpEnd()->format('c'),
            'dateEventStart' => $cfp->getDateEventStart()->format('c'),
            'dateEventEnd'   => $cfp->getDateEventEnd()->format('c'),
            'name'           => $cfp->getName(),
            'uri'            => $cfp->getUri(),
            'hash'           => $cfp->getHash(),
            'timezone'       => $cfp->getTimezone()->getName(),
            'description'    => $cfp->getDescription(),
            'eventUri'       => $cfp->getEventUri(),
            'iconUri'        => $cfp->getIconUri(),
            'latitude'       => $cfp->getLatitude(),
            'longitude'      => $cfp->getLongitude(),
            'location'       => $cfp->getLocation(),
            'tags'           => implode(',', $cfp->getTags()),
            'fetchHash'      => $fetchHash,
            'lastUpdate'     => (new \DateTime('now', new \DateTimezone('UTC')))->format('c'),
        ];

        if ($statement->execute($values)) {
            return $values['hash'];
        }

        throw new \UnexpectedValueException('The CfP could not be updated', 400);
    }


    public function select($hash = null)
    {
        $statement = 'SELECT * FROM `cfp`';
        $values = [];
        if ($hash !== null) {
            $statement .= ' WHERE `hash`= :hash';
            $values['hash'] = $hash;
        }



        $statement = $this->pdo->prepare($statement);

        $list = new \Callingallpapers\Api\Entity\CfpList();
        $statement->execute($values);
        $content = $statement->fetchAll();
        if (count($content) < 1) {
            $this->logger->addDebug('No CFPs found for hash', ['hash' => $hash]);
            throw new \UnexpectedValueException('No CFPs found', 404);
        }

        $this->logger->addDebug('Found ' . count($content) . ' elements with hash', ['hash' => $hash]);
        foreach ($content as $item) {
            $cfp = new \Callingallpapers\Api\Entity\Cfp();
            $cfp->setName($item['name']);
            $cfp->setDateCfpEnd(new \DateTimeImmutable($item['dateCfpEnd']));
            $cfp->setDateCfpStart(new \DateTimeImmutable($item['dateCfpStart']));
            $cfp->setUri($item['uri']);
            $cfp->setTimezone(new \DateTimeZone($item['timezone']));
            $cfp->setDateEventStart(new \DateTimeImmutable($item['dateEventStart']));
            $cfp->setDateEventEnd(new \DateTimeImmutable($item['dateEventEnd']));
            $cfp->setDescription($item['description']);
            $cfp->setEventUri($item['eventUri']);
            $cfp->setIconUri($item['iconUri']);
            $cfp->setLatitude($item['latitude']);
            $cfp->setLongitude($item['longitude']);
            $cfp->setLocation($item['location']);
            $cfp->setTags(explode(',', $item['tags']));
            $cfp->setLastUpdated(new \DateTimeImmutable($item['lastUpdate']));

            $list->add($cfp);
        }

        return $list;
    }

    public function delete($hash)
    {
        $statement = 'DELETE FROM `cfp` WHERE `hash` = :hash';

        $statement = $this->pdo->prepare($statement);

        return $statement->execute(['hash' => $hash]);
    }
}