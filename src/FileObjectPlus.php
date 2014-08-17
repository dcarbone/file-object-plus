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

        $this->rewind();

        while ($this->valid())
        {
            if (stripos(trim($this->current()), $string) !== false)
                $count++;

            $this->next();
        }

        $this->rewind();

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

        $linesTotal = 0;
        $lines = array();

        if ($offset === 0)
            $this->rewind();
        else
            $this->seek($offset + 1);

        if ($search === null)
        {
            while($this->valid())
            {
                if ($linesTotal === $limit)
                    break;

                $lines[] = trim($this->current());
                $linesTotal++;

                $this->next();
            }
        }
        else
        {
            $search = (string)$search;

            while($this->valid())
            {
                if ($linesTotal === $limit)
                    break;

                $current = trim($this->current());
                if (stripos($current, $search) !== false)
                {
                    $lines[] = $current;
                    $linesTotal++;
                }

                $this->next();
            }
        }

        $this->rewind();

        return $lines;
    }
}