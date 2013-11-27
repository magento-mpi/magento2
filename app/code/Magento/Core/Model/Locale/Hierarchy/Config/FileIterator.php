<?php
/**
 * Hierarchy config file resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Locale\Hierarchy\Config;

class FileIterator implements \Iterator
{

    /**
     * @var array
     */
    protected $paths;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var
     */
    protected $directoryRead;

    public function __construct(
        \Magento\Filesystem $filesystem,
        array $paths
    ){
        $this->paths            = $paths;
        $this->position         = 0;
        $this->directoryRead    = $filesystem->getDirectoryRead(\Magento\Filesystem::APP);
    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->directoryRead->readFile($this->paths[$this->position]);
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->paths[$this->position]);
    }
}
