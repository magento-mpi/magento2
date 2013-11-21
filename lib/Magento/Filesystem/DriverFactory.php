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
    const BASE = 'base';

    const SOCKET = 'socket';

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Filesystem\DriverInterface[]
     */
    protected $_drivers = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get a driver instance according the given scheme.
     *
     * @param string $directoryDriverClass [optional]
     * @return \Magento\Filesystem\DriverInterface
     * @throws \Exception
     */
    public function get($directoryDriverClass = '\Magento\Filesystem\Driver\Base')
    {
        $driver = $this->objectManager->get($directoryDriverClass);
        if (!$driver instanceof \Magento\Filesystem\DriverInterface) {
            throw new \Exception("Invalid filesystem driver class: " . $directoryDriverClass);
        }

        return $driver;
    }
}
