<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\File;

use Magento\Framework\Filesystem\DriverInterface;

class WriteFactory
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
        return new \Magento\Framework\Filesystem\File\Write($path, $driver, $mode);
    }
}
