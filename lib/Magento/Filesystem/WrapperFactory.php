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
    public function get($protocolCode, \Magento\Filesystem\DriverInterface $driver)
    {
        $class = '\\Magento\\Filesystem\\Protocol\\' . $protocolCode;

        return $this->wrappers[$protocolCode];
    }
}
