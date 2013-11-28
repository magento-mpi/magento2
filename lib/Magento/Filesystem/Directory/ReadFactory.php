<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\Directory;

class ReadFactory
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
        $factory = new \Magento\Filesystem\File\ReadFactory();
        return new \Magento\Filesystem\Directory\Read($config, $factory, $driver);
    }
}