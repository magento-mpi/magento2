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
     * Return specific wrapper
     *
     * @param string $protocolCode
     * @param string $classCode
     * @param DriverInterface $driver
     */
    public function get($protocolCode, $classCode, \Magento\Filesystem\DriverInterface $driver) {
        if (!isset($this->wrappers[$protocolCode])) {
            $protocolInstance = new $classCode($driver);
            $this->wrappers[$protocolCode] = $protocolInstance;
        }
        return $this->wrappers[$protocolCode];
    }
}
