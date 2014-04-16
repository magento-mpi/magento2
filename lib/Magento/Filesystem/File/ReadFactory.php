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
     * @var \Magento\Filesystem\DriverFactory
     */
    protected $driverFactory;

    /**
     * @param \Magento\Filesystem\DriverFactory $driverFactory
     */
    public function __construct(\Magento\Filesystem\DriverFactory $driverFactory)
    {
        $this->driverFactory = $driverFactory;
    }

    /**
     * Create a readable file
     *
     * @param string $path
     * @param string|null $protocol [optional]
     * @param DriverInterface $driver [optional]
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function create($path, $protocol = null, DriverInterface $driver = null)
    {
        $driver = $protocol ? $this->driverFactory->get($protocol, $driver) : $driver;
        return new \Magento\Filesystem\File\Read($path, $driver);
    }
}
