<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem;

/**
 * Class WrapperFactory
 *
 */
class WrapperFactory
{
    /**
     * @var array
     */
    private $wrappers = array();

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
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
     * Return specific wrapper
     *
     * @param string $protocolCode
     * @param DriverInterface $driver
     * @return WrapperInterface
     */
    public function get($protocolCode, DriverInterface $driver)
    {
        $wrapperClass = $this->directoryList->getProtocolConfig($protocolCode)['driver'];

        if (!isset($this->wrappers[$protocolCode])) {
            $this->wrappers[$protocolCode] = new $wrapperClass($driver);
        }

        return $this->wrappers[$protocolCode];
    }
}
