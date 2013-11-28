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
class FileIterator implements \Iterator
{
    /**
     * @var array
     */
    protected $cached = array();

    /**
     * @var array
     */
    protected $paths;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * @param \Magento\Filesystem\Directory\ReadInterface $directory
     * @param array $paths
     */
    public function __construct(
        \Magento\Filesystem\Directory\ReadInterface $directory,
        array $paths
    ){
        $this->paths            = $paths;
        $this->position         = 0;
        $this->directoryRead    = $directory;
    }

    /**
     *
     */
    function rewind()
    {
        reset($this->paths);
    }

    /**
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
     * @return mixed
     */
    function key()
    {
        return current($this->paths);
    }

    /**
     *
     */
    function next()
    {
        next($this->paths);
    }

    /**
     * @return bool
     */
    function valid()
    {
        return (boolean)$this->key();
    }

    /**
     * @return array
     */
    public function getContents()
    {
        return $this->paths;
    }
}
