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
     * Create a readable file
     *
     * @param string $path
     * @param DriverInterface $driver
     * @param string $mode
     * @return \Magento\Filesystem\File\WriteInterface
     */
    public function create($path, DriverInterface $driver, $mode)
    {
        return new \Magento\Filesystem\File\Write($path, $driver, $mode);
    }
}