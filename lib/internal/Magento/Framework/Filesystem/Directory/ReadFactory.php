<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\Directory;

class ReadFactory
{
    /**
     * Create a readable directory
     *
     * @param array $config
     * @param \Magento\Framework\Filesystem\DriverFactory $driverFactory
     * @return ReadInterface
     */
    public function create(array $config, \Magento\Framework\Filesystem\DriverFactory $driverFactory)
    {
        $protocolCode = isset($config['protocol']) ? $config['protocol'] : null;
        $driverClass = isset($config['driver']) ? $config['driver'] : null;
        $driver = $driverFactory->get($protocolCode, $driverClass);
        $factory = new \Magento\Framework\Filesystem\File\ReadFactory($driverFactory);

        return new Read($config, $factory, $driver);
    }
}
