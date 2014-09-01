<?php namespace DCarbone;

/**
 * Class FileObjectPlus
 * @package DCarbone
 */
class FileObjectPlus extends \SplFileObject
{
    /** @var int */
    protected $lineCount = 0;

    /**
     * @param $filename
     * @param string $open_mode
     * @param bool $use_include_path
     * @param null|resource $context
     * @throws \InvalidArgumentException
     */
    public function __construct($filename, $open_mode = 'r', $use_include_path = false, $context = null)
    {
        if (!is_string($filename))
            throw new \InvalidArgumentException('SplFileObject::__construct - Argument 1 expected to be string, "'.gettype($filename).'" seen.');

        if (is_resource($context))
            parent::__construct($filename, $open_mode, $use_include_path, $context);
        else
            parent::__construct($filename, $open_mode, $use_include_path);

        if (DIRECTORY_SEPARATOR === '/')
            $this->lineCount = (int)trim(shell_exec('wc -l "'.$this->getRealPath().'"'));
        else
            $this->lineCount = (int)trim(shell_exec('type "'.$this->getRealPath().'" | find /c /v "~~~"'));
    }

    /**
     * @return int
     */
    public function getLineCount()
    {
        return $this->lineCount;
    }

    /**
     * @param string|int|bool|float $string
     * @return int
     * @throws \InvalidArgumentException
     */
    public function getLineCountLike($string)
    {
        if (!is_scalar($string))
            throw new \InvalidArgumentException('FileObjectPlus::getLineCountLike - Argument 1 expected to be scalar type, "'.gettype($string).'" seen.');

        $string = (string)$string;

        if ($string === '')
            return $this->lineCount;

        $count = 0;

        parent::rewind();

        while (parent::valid())
        {
            if (stripos(trim(parent::current()), $string) !== false)
                $count++;

            parent::next();
        }

        parent::rewind();

        return $count;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param mixed $search
     * @return array
     * @throws \InvalidArgumentException
     */
    public function paginateLines($offset = 0, $limit = 25, $search = null)
    {
        if (!is_int($offset))
            throw new \InvalidArgumentException('FileObjectPlus::paginateLines - Argument 1 expected to be integer, '.gettype($offset).' seen.');
        if ($offset < 0)
            throw new \InvalidArgumentException('FileObjectPlus::paginateLines - Argument 1 expected to be >= 0, "'.$offset.'" seen.');

        if (!is_int($limit))
            throw new \InvalidArgumentException('FileObjectPlus::paginateLines - Argument 2 expected to be integer, '.gettype($limit).' seen.');
        if ($limit < -1)
            throw new \InvalidArgumentException('FileObjectPlus::paginateLines - Argument 2 must be >= -1, "'.$limit.'" seen.');

        if ($search !== null && !is_scalar($search))
            throw new \InvalidArgumentException('FileObjectPlus::paginateLines - Argument 3 expected to be scalar value or null, '.gettype($search).' seen.');

        if ($limit === -1)
            $limit = $this->lineCount;

        if ($search === null)
            return $this->paginateLinesNoSearch($offset, $limit);
        else
            return $this->paginateLinesSearch($offset, $limit, $search);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function paginateLinesNoSearch($offset, $limit)
    {
        $linesTotal = 0;
        $lines = array();

        if ($offset === 0)
            parent::rewind();
        else
            parent::seek(($offset + 1));

        while(parent::valid())
        {
            if ($linesTotal === $limit)
                break;

            if (($current = trim(parent::current())) !== '')
            {
                $lines[] = $current;
                $linesTotal++;
            }

            parent::next();
        }

        parent::rewind();

        return $lines;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param mixed $search
     * @return array
     */
    protected function paginateLinesSearch($offset, $limit, $search)
    {
        $linesTotal = 0;
        $lines = array();
        $linei = 0;

        if ($offset === 0)
            $offset = -1;

        parent::rewind();

        $search = (string)$search;

        while($this->valid())
        {
            if ($linesTotal === $limit)
                break;

            $current = trim(parent::current());
            if ($current !== '' && stripos($current, $search) !== false)
            {
                if ($linei++ > $offset)
                {
                    $lines[] = $current;
                    $linesTotal++;
                }
            }

            parent::next();
        }

        parent::rewind();

        return $lines;
    }
}