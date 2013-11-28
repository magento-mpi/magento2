<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

class FileIterator implements \Iterator
{
    /**
     * @var array
     */
    protected $cached;

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

    function rewind()
    {
        $this->position = 0;
    }

    function current()
    {
        if (!isset($this->cached[$this->position])) {
            $this->cached[$this->position] = $this->directoryRead->readFile($this->paths[$this->position]);
        }
        return $this->cached[$this->position];

    }

    function key()
    {
        return $this->position;
    }

    function next()
    {
        ++$this->position;
    }

    function valid()
    {
        return isset($this->paths[$this->position]);
    }
}
