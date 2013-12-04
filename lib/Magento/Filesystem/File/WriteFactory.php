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
    public function __construct($wrapperFactory)
    {
        // wrapper factory here
    }

    /**
     * Create a readable file
     *
     * @param string $path
     * @param DriverInterface $driver
     * @param string $mode
     * @return \Magento\Filesystem\File\WriteInterface
     */
    public function create($path, DriverInterface $driver, $mode, $protocol = null)
    {
        if ($protocol) {
            $wrapperFactory->
            $wrapper = new Wrapper($driver);
        }
        return new \Magento\Filesystem\File\Write($path, $wrapper, $mode);
    }
}