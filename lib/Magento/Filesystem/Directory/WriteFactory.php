<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\Directory;

class WriteFactory
{

    /**
     * Create a readable directory
     *
     * @param array $config
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function create(array $config)
    {
        $directoryDriver = isset($config['driver']) ? $config['driver'] : '\Magento\Filesystem\Driver\Base';
        $driver = new $directoryDriver();
        $factory = new \Magento\Filesystem\File\WriteFactory();
        $wrapperFactory = new \Magento\Filesystem\WrapperFactory();

        return new \Magento\Filesystem\Directory\Write($config, $factory, $driver, $wrapperFactory);
    }
}
