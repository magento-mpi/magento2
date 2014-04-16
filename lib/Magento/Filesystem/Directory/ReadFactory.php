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
     * @param \Magento\Filesystem\DriverFactory $driverFactory
     * @return ReadInterface
     */
    public function create(array $config, \Magento\Filesystem\DriverFactory $driverFactory)
    {
        $protocolCode = isset($config['protocol']) ? $config['protocol'] : null;
        $driverClass = isset($config['driver']) ? $config['driver'] : null;
        $driver = $driverFactory->get($protocolCode, $driverClass);
        $factory = new \Magento\Filesystem\File\ReadFactory($driverFactory);

        return new Read($config, $factory, $driver);
    }
}
