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
     * Create a readable file.
     *
     * @param string $path
     * @param string|null $protocol [optional]
     * @param DriverInterface $driver [optional]
     * @param string $mode [optional]
     * @return Write
     */
    public function create($path, $protocol = null, DriverInterface $driver = null, $mode = 'r')
    {
        $driver = $protocol ? $this->driverFactory->get($protocol, get_class($driver)) : $driver;
        return new \Magento\Filesystem\File\Write($path, $driver, $mode);
    }
}
