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
     * @param string|null $protocol
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function create($path, DriverInterface $driver, $protocol = null)
    {
        $wrapper = $driver;
        if ($protocol) {
            $wrapper = $this->wrapperFactory->get($protocol, $driver);
        }
        return new \Magento\Filesystem\File\Read($path, $wrapper);
    }
}