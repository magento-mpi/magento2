<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\File;

use Magento\Framework\Filesystem\DriverInterface;

class ReadFactory
{
    /**
     * @var \Magento\Framework\Filesystem\DriverFactory
     */
    protected $driverFactory;

    /**
     * @param \Magento\Framework\Filesystem\DriverFactory $driverFactory
     */
    public function __construct(\Magento\Framework\Filesystem\DriverFactory $driverFactory)
    {
        $this->driverFactory = $driverFactory;
    }

    /**
     * Create a readable file
     *
     * @param string $path
     * @param string|null $protocol [optional]
     * @param DriverInterface $driver [optional]
     * @return \Magento\Framework\Filesystem\File\ReadInterface
     */
    public function create($path, $protocol = null, DriverInterface $driver = null)
    {
        $driver = $protocol ? $this->driverFactory->get($protocol, get_class($driver)) : $driver;
        return new \Magento\Framework\Filesystem\File\Read($path, $driver);
    }
}
