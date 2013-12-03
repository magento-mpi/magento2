<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\Protocol;

/**
 * Class File
 *
 * @package Magento\Filesystem\Protocol
 */
class File
{
    /**
     * Wrapper scheme code
     */
    const SCHEME = \Magento\Filesystem::WRAPPER_STREAM_FILE;

    /**
     * @var \Magento\Filesystem\DriverInterface
     */
    protected $driver;

    /**
     * @param \Magento\Filesystem\DriverInterface $driver
     */
    public function __construct(\Magento\Filesystem\DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function readFile($path)
    {
        return $this->driver->fileGetContents(static::SCHEME . '://' . $path);
    }
}
