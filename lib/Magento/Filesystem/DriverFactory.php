<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem;

class DriverFactory
{
    /**
     * @var \Magento\Filesystem\DriverInterface[]
     */
    protected $drivers = array();

    /**
     * Get a driver instance according the given scheme.
     *
     * @param null $driverClass
     * @param DriverInterface $driver
     * @return DriverInterface
     * @throws FilesystemException
     */
    public function get($driverClass = null, DriverInterface $driver = null)
    {
        if ($driverClass === null) {
            $driverClass = '\Magento\Filesystem\Driver\File';
        }
        if (!isset($this->drivers[$driverClass])) {
            $this->drivers[$driverClass] = new $driverClass($driver);
            if (!$this->drivers[$driverClass] instanceof \Magento\Filesystem\DriverInterface) {
                throw new \Magento\Filesystem\FilesystemException("Invalid filesystem driver class: " . $driverClass);
            }
        }
        return $this->drivers[$driverClass];
    }
}
