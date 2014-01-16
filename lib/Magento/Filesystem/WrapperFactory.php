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

/**
 * Class WrapperFactory
 *
 * @package Magento\Filesystem
 */
class WrapperFactory
{
    /**
     * @var array
     */
    private $wrappers = array();

    /**
     * @var \Magento\Filesystem\DirectoryList
     */
    protected $directoryList;

    public function __construct(\Magento\Filesystem\DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * Return specific wrapper
     *
     * @param string $protocolCode
     * @param DriverInterface $driver
     */
    public function get($protocolCode, \Magento\Filesystem\DriverInterface $driver)
    {
        $wrapperClass = $this->directoryList->getProtocolConfig($protocolCode)['driver'];

        if (!isset($this->wrappers[$protocolCode])) {
            $this->wrappers[$protocolCode] = new $wrapperClass($driver);
        }

        return $this->wrappers[$protocolCode];
    }
}
