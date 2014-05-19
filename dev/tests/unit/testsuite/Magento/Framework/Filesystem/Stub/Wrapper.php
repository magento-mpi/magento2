<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Filesystem\Stub;

/**
 * Class Wrapper
 */
class Wrapper
{
    /**
     * Driver
     *
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $driver;

    public function __construct(\Magento\Framework\Filesystem\DriverInterface $driver)
    {
        $this->driver = $driver;
    }
} 