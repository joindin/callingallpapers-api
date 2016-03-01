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

namespace Callingallpapers\Api\Entity;

class CfpList implements \Iterator, \Countable
{
    protected $cfps = [];

    public function add(Cfp $cfp)
    {
        if (! $this->has($cfp)) {
            $this->cfps[] = $cfp;
        }
    }

    /**
     * Check whether the given CfP already exists in the List
     *
     * @param $cfp
     */
    public function has($cfp)
    {
        return in_array($cfp, $this->cfps, true);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->cfps);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->cfps);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->cfps);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->cfps);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return null !== $this->key();
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->cfps);
    }

    /**
     * Sort the List according to the sorter
     *
     * @param \Callingallpapers\Api\CfpSortInterface $sorter
     *
     * @return void
     */
    public function sort(\Callingallpapers\Api\CfpSortInterface $sorter)
    {
        usort($this->cfps, [$sorter, 'sort']);
    }
}
