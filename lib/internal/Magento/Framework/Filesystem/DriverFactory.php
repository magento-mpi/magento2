<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem;

class DriverFactory
{
    /**
     * @var \Magento\Framework\Filesystem\DriverInterface[]
     */
    protected $drivers = array();

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param DirectoryList $directoryList
     */
    public function __construct(DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * Get a driver instance according the given scheme.
     *
     * @param null|string $protocolCode
     * @param string $driverClass
     * @return DriverInterface
     * @throws FilesystemException
     */
    public function get($protocolCode = null, $driverClass = null)
    {
        if (!$driverClass) {
            $driverClass = $protocolCode ? $this->directoryList->getProtocolConfig($protocolCode)['driver']
                : '\Magento\Framework\Filesystem\Driver\File';
        }
        if (!isset($this->drivers[$driverClass])) {
            $this->drivers[$driverClass] = new $driverClass();
            if (!$this->drivers[$driverClass] instanceof DriverInterface) {
                throw new FilesystemException("Invalid filesystem driver class: " . $driverClass);
            }
        }
        return $this->drivers[$driverClass];
    }
}
