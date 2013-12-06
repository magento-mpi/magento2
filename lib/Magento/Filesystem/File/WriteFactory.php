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
     * @var \Magento\Filesystem\WrapperFactory
     */
    protected $wrapperFactory;

    public function __construct(\Magento\Filesystem\WrapperFactory $wrapperFactory)
    {
        $this->wrapperFactory = $wrapperFactory;
    }

    /**
     * Create a readable file
     *
     * @param string $path
     * @param DriverInterface $driver
     * @param string $mode
     * @param string|null $protocol
     * @return \Magento\Filesystem\File\WriteInterface
     */
    public function create($path, DriverInterface $driver, $protocol = null, $mode = 'r')
    {
        $wrapper = $driver;
        if ($protocol) {
            $wrapper = $this->wrapperFactory->get($protocol, $driver);
        }
        return new \Magento\Filesystem\File\Write($path, $wrapper, $mode);
    }
}