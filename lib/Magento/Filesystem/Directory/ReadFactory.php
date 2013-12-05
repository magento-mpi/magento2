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
     * @param \Magento\Filesystem\WrapperFactory $wrapperFactory
     *
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function create(array $config, \Magento\Filesystem\WrapperFactory $wrapperFactory)
    {
        $directoryDriver = isset($config['driver']) ? $config['driver'] : '\Magento\Filesystem\Driver\Local';
        $driver = new $directoryDriver();
        $factory = new \Magento\Filesystem\File\ReadFactory($wrapperFactory);

        return new \Magento\Filesystem\Directory\Read($config, $factory, $driver);
    }
}