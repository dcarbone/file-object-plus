<?php namespace DCarbone;

/*
    Modified SplFileObject class that adds Countable interface and Pagination methods
    Copyright (C) 2013-2015  Daniel Paul Carbone (daniel.p.carbone@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 */

use DCarbone\Helpers\FileShellHelper;
use DCarbone\Helpers\SystemOSTypeHelper;

/**
 * Class FileObjectPlus
 * @package DCarbone
 */
class FileObjectPlus extends \SplFileObject implements \Countable
{
    /**
     * TODO: See if way to preserve current line of underlying \SplFileObject
     *
     * @param mixed $term
     * @return int
     * @throws \InvalidArgumentException
     */
    public function countLinesContaining($term)
    {
        if (is_bool($term) || is_null($term))
            trigger_error(sprintf('%s::countLinesLike - %s input seen, which results in empty string when typecast. Please check input value.', get_class($this), gettype($term)));

        if (is_scalar($term) || (is_object($term) && method_exists($term, '__toString')))
        {
            $term = (string)$term;

            if ($term === '')
                return count($this);

            $count = 0;

            for(parent::rewind(); parent::valid(); parent::next())
            {
                if (stripos(parent::current(), $term) !== false)
                    $count++;
            }

            parent::rewind();

            return $count;
        }

        throw new \InvalidArgumentException(get_class($this).'::countLinesLike - Argument 1 expected to be castable to string, "'.gettype($term).'" seen.');
    }

    /**
     * TODO: See if way to preserve current line of underlying \SplFileObject
     *
     * @param int $offset
     * @param int $limit
     * @param int|float|string|null $term
     * @param bool $includeEmpty Only used if $term is null
     * @return array
     */
    public function paginateLines($offset = 0, $limit = 25, $term = null, $includeEmpty = true)
    {
        if (!is_int($offset))
            throw new \InvalidArgumentException('Argument 1 expected to be integer, '.gettype($offset).' seen.');
        if ($offset < 0)
            throw new \InvalidArgumentException('Argument 1 expected to be >= 0, "'.$offset.'" seen.');

        if (!is_int($limit))
            throw new \InvalidArgumentException('Argument 2 expected to be integer, '.gettype($limit).' seen.');
        if ($limit < -1)
            throw new \InvalidArgumentException('Argument 2 must be >= -1, "'.$limit.'" seen.');

        if ($limit === -1)
            $limit = count($this);

        if ($term === null)
            return $this->paginateLinesNoSearch($offset, $limit, $includeEmpty);

        return $this->paginateLinesSearch($offset, $limit, $term);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        static $cmd;
        if (!isset($cmd))
            $cmd = SystemOSTypeHelper::invoke() === 'windows' ? 'windows-line-count' : 'linux-line-count';

        return FileShellHelper::executeCommand($this->getRealPath(), $cmd);
    }

    //---------------

    /**
     * @param int $offset
     * @param int $limit
     * @param bool $includeEmptyLines
     * @return array
     */
    protected function paginateLinesNoSearch($offset, $limit, $includeEmptyLines = true)
    {
        $returnLines = array();
        $returnLinesCount = 0;

        if ($offset === 0)
            parent::rewind();
        else
            parent::seek(($offset + 1));

        if ($includeEmptyLines)
        {
            while(parent::valid() && $returnLinesCount < $limit)
            {
                $returnLines[] = parent::current();
                $returnLinesCount++;
                parent::next();
            }
        }
        else
        {
            while(parent::valid() && $returnLinesCount < $limit)
            {
                if (($current = trim(parent::current())) !== '')
                {
                    $returnLines[] = $current;
                    $returnLinesCount++;
                }
                parent::next();
            }
        }

        parent::rewind();

        return $returnLines;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param string $term
     * @return array
     */
    protected function paginateLinesSearch($offset, $limit, $term)
    {
        $returnLines = array();
        $returnLinesCount = 0;
        $matchingLinesCount = -1;

        if ($offset === 0)
            $offset = -1;

        parent::rewind();

        if (is_bool($term) || is_null($term))
            trigger_error(sprintf('%s::paginateLines - %s search input seen, which results in empty string when typecast. Please check input value.', get_class($this), gettype($term)));

        if (is_scalar($term) || (is_object($term) && method_exists($term, '__toString')))
        {
            $term = (string)$term;
            while(parent::valid() && $returnLinesCount < $limit)
            {
                if (($current = parent::current()) !== '' && stripos($current, $term) !== false && ++$matchingLinesCount > $offset)
                {
                    $returnLines[] = $current;
                    $returnLinesCount++;
                }

                parent::next();
            }

            parent::rewind();

            return $returnLines;
        }

        throw new \InvalidArgumentException(get_class($this).'::paginateLines - Search value expected to be castable to string, "'.gettype($term).'" seen.');
    }
}