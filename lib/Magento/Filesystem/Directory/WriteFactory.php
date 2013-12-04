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
     * @param \Magento\Filesystem\WrapperFactory $wrapperFactory
     * @return \Magento\Filesystem\File\ReadInterface
     */
    public function create(array $config, \Magento\Filesystem\WrapperFactory $wrapperFactory)
    {
        $directoryDriver = isset($config['driver']) ? $config['driver'] : '\Magento\Filesystem\Driver\Base';
        $driver = new $directoryDriver();
        $factory = new \Magento\Filesystem\File\WriteFactory($wrapperFactory);

        return new \Magento\Filesystem\Directory\Write($config, $factory, $driver);
    }
}
