<?php

/**
 * Copyright (c) 2016-2016} Andreas Heigl<andreas@heigl.org>
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
 * @copyright 2016-2016 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     19.01.2016
 * @link      http://github.com/joindin/callingallpapers-api
 */

namespace Callingallpapers\Api\PersistenceLayer;

use Callingallpapers\Api\Entity\Cfp;
use Camel\CaseTransformer;
use Camel\Format\CamelCase;
use Camel\Format\SnakeCase;
use DateTime;
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
        try {
            $this->select($cfp->getHash());
            $cfpExists = true;
        } catch (\UnexpectedValueException $e) {
            $cfpExists = false;
        }

        if ($cfpExists) {
            throw new \UnexpectedValueException(sprintf(
                'There is already a CFP with URL "%1$s".',
                $cfp->getEventUri()
            ), 400);
        }

        if ($cfp->getDateCfpStart()->getTimestamp() < 10000) {
            $cfp->setDateCfpStart(new \DateTimeImmutable());
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
        $oldValues = $this->select($fetchHash);
        if ($oldValues->count() != 1) {
            throw new \UnexpectedValueException('There is no CFP with this URL.', 404);
        }
        $statementElements = [];
        $values = [];
        $oldValues = $oldValues->current();
        $options = [
            'dateCfpStart',
            'dateCfpEnd',
            'name',
            'uri',
            'hash',
            'timezone',
            'dateEventStart',
            'dateEventEnd',
            'description',
            'eventUri',
            'iconUri',
            'latitude',
            'longitude',
            'location',
            'tags',
            'source',
        ];

        foreach ($options as $option) {
            $method = 'get' . $option;
           // Merge values from tags and source before comparing!
            if (in_array($option, ['tags', 'source'])) {
                $setter = 'set' . $option;
                $cfp->$setter(array_merge($oldValues->$method(), $cfp->$method()));
            }
            if ($cfp->$method() !== $oldValues->$method()) {
                if ($option == 'dateCfpStart' && $cfp->$method()->getTimestamp() < 100000) {
                    continue;
                }
                $statementElements[] = '`' . $option . '` = :' . $option;
                $values[$option]     = $cfp->$method();
                if ($values[$option] instanceof \DateTimeInterface) {
                    $values[$option] = $values[$option]->format('c');
                }
                if ($values[$option] instanceof \DateTimeZone) {
                    $values[$option] = $values[$option]->getName();
                }
                if (is_array($values[$option])) {
                    $values[$option] = implode(',', array_unique($values[$option]));
                }
            }
        }

        // No values to update, just, return
        if (empty($values)) {
            return $cfp->getHash();
        }

        $statement = 'UPDATE `cfp` SET '
                   . implode(',', $statementElements) . ','
                   . '`lastUpdate` = :lastUpdate '
                   . 'WHERE `hash` = :fetchHash';
        $statement = $this->pdo->prepare($statement);
        $values['fetchHash']  = $fetchHash;
        $values['lastUpdate'] = (new \DateTime('now', new \DateTimezone('UTC')))->format('c');

        if ($statement->execute($values)) {
            return $cfp->getHash();
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
            throw new \UnexpectedValueException('No CFPs found', 404);
        }

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
            $cfp->setSource(explode(',', $item['source']));

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

    public function getCurrent(\DateTimeInterface $startDate = null, \DateTimeInterface $endDate = null)
    {
        $statement = 'SELECT * FROM `cfp` WHERE dateCfpEnd > :end AND dateCfpStart < :start';

        $statement = $this->pdo->prepare($statement);

        $now = new \DateTime();
        if (null === $startDate) {
            $startDate = $now;
        }
        if (null === $endDate) {
            $endDate = $now;
        }

        $list = new \Callingallpapers\Api\Entity\CfpList();
        $statement->execute([
            'end'   => $endDate->format('c'),
            'start' => $startDate->format('c'),
        ]);

        $content = $statement->fetchAll();
        if (count($content) < 1) {
            throw new \UnexpectedValueException('No CFPs found', 404);
        }

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
            $cfp->setSource(explode(',', $item['source']));

            $list->add($cfp);
        }

        return $list;
    }

    public function search(array $parameters)
    {
        $fields = [
            'name',
            'tags',
            'date_cfp_end',
            'date_cfp_start',
            'date_event_end',
            'date_event_start',
            'latitude',
            'longitude',

        ];
        $transformer = new CaseTransformer(new SnakeCase(), new CamelCase());

        $statement = 'SELECT * FROM `cfp` WHERE ';
        $values    = [];
        $where = [];
        foreach ($parameters as $key => $value) {
            if (! in_array($key, $fields)) {
                continue;
            }
            if (! is_array($value)){
                $value = [$value];
            }
            foreach($value as $itemkey => $item) {
                $compare = '=';
                if (in_array($key, [
                    'date_cfp_end',
                    'date_cfp_start',
                    'date_event_end',
                    'date_event_start'
                ])) {
                    if (array_key_exists($key . '_compare', $parameters) && isset($parameters[$key . '_compare'][$itemkey]) && in_array($parameters[$key . '_compare'][$itemkey], ['=', '<', '>', '<>'])) {
                        $compare = $parameters[$key . '_compare'][$itemkey];
                    }
                    $where[] = 'datetime(`' . $transformer->transform($key) . '`) ' . $compare . ' datetime(:' . $key . '_' . $itemkey . ')';
//                    $where[] = '`' . $transformer->transform($key) . '` ' . $compare . ' CONVERT_TZ(:' . $key . ', timezone, \'UTC\')';
                    $value = (new DateTime($item))->setTimezone(new \DateTimeZone('UTC'))->format('c');
                } else {
                    $where[] = '`' . $transformer->transform($key) . '` ' . $compare . ' :' . $key;
                }
                $values[$key . '_' . $itemkey] = $value;
            }
        }
        if (! $where) {
            throw new \UnexpectedValueException('No CFPs found', 404);
        }

        $statement .= implode(' AND ', $where);

        $statement = $this->pdo->prepare($statement);

        $statement->execute($values);
        error_log($statement->queryString);
        $content = $statement->fetchAll();
        if (count($content) < 1) {
            throw new \UnexpectedValueException('No CFPs found', 404);
        }

        $list = new \Callingallpapers\Api\Entity\CfpList();
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
         //   $cfp->setSource(explode(',', $item['source']));

            $list->add($cfp);
        }

        return $list;
    }
}
