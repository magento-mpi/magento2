<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\File;

use Magento\Filesystem\DriverInterface;

class ReadFactory
{
    /**
     * Create a readable file
     *
     * @param string $path
     * @param DriverInterface $driver
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function create($path, DriverInterface $driver)
    {
        return new \Magento\Filesystem\File\Read($path, $driver);
    }
}