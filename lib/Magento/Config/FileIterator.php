<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

/**
 * Class FileIterator
 */
class FileIterator implements \Iterator, \Countable
{
    /**
     * Cache
     *
     * @var array
     */
    protected $cached = array();

    /**
     * Paths
     *
     * @var array
     */
    protected $paths = array();

    /**
     * Position
     *
     * @var int
     */
    protected $position;

    /**
     * Read directory
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * Constructor
     *
     * @param \Magento\Filesystem\Directory\ReadInterface $directory
     * @param array $paths
     */
    public function __construct(
        \Magento\Filesystem\Directory\ReadInterface $directory,
        array $paths
    ) {
        $this->paths            = $paths;
        $this->position         = 0;
        $this->directoryRead    = $directory;
    }

    /**
     *Rewind
     *
     * @return mixed the value of the first array element, or false if the array is empty
     */
    function rewind()
    {
        reset($this->paths);
    }

    /**
     * Current
     *
     * @return string
     */
    function current()
    {
        if (!isset($this->cached[$this->key()])) {
            $this->cached[$this->key()] = $this->directoryRead->readFile($this->key());
        }
        return $this->cached[$this->key()];

    }

    /**
     * Key
     *
     * @return mixed
     */
    function key()
    {
        return current($this->paths);
    }

    /**
     * Next
     *
     * @return mixed the array value in the next place that's pointed to by the
     * internal array pointer, or false if there are no more elements.
     */
    function next()
    {
        next($this->paths);
    }

    /**
     * Valid
     *
     * @return bool
     */
    function valid()
    {
        return (boolean)$this->key();
    }

    /**
     * Convert to an array
     *
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this as $item) {
            $result[$this->key()] = $item;
        }
        return $result;
    }

    /**
     * Count
     *
     * @return int
     */
    public function count()
    {
        return count($this->paths);
    }
}
