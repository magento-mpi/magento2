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
     * @param string $driverClass [optional]
     * @return \Magento\Filesystem\DriverInterface
     * @throws \Exception
     */
    public function get($driverClass = '\Magento\Filesystem\Driver\Local')
    {
        if (!isset($this->drivers[$driverClass])) {
            $this->drivers[$driverClass] = new $driverClass;
            if (!$this->drivers[$driverClass] instanceof \Magento\Filesystem\DriverInterface) {
                throw new \Exception("Invalid filesystem driver class: " . $driverClass);
            }
        }
        return $this->drivers[$driverClass];
    }
}
