<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\File;

use Magento\Filesystem\DriverInterface;

class WriteFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create a readable file
     *
     * @param string $path
     * @param DriverInterface $driver
     * @param string $mode
     * @return \Magento\Filesystem\File\WriteInterface
     */
    public function create($path, DriverInterface $driver, $mode)
    {
        return $this->objectManager->create('Magento\Filesystem\File\Write',
            array(
                'path' => $path,
                'driver' => $driver,
                'mode' => $mode
            ));
    }
}