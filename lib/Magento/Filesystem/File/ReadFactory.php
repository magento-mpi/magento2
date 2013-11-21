<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\File;

use Magento\Filesystem\DriverInterface;

class ReadFactory
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
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function create($path, DriverInterface $driver)
    {
        return $this->objectManager->create('Magento\Filesystem\File\Read', array(
            'path' => $path,
            'driver' => $driver
        ));
    }
}