<?php
/**
 * Profiler driver factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Profiler\Driver;

class Factory
{
    /**
     * Default driver type
     *
     * @var string
     */
    protected $_defaultDriverType;

    /**
     * Default driver class prefix
     *
     * @var string
     */
    protected $_defaultDriverPrefix;

    /**
     * Constructor
     *
     * @param string $defaultDriverPrefix
     * @param string $defaultDriverType
     */
    public function __construct($defaultDriverPrefix = 'Magento_Profiler_Driver_', $defaultDriverType = 'standard')
    {
        $this->_defaultDriverPrefix = $defaultDriverPrefix;
        $this->_defaultDriverType = $defaultDriverType;
    }

    /**
     * Create instance of profiler driver
     *
     * @param array $config|null
     * @return \Magento\Profiler\DriverInterface
     * @throws \InvalidArgumentException
     */
    public function create(array $config = null)
    {
        $type = isset($config['type']) ? $config['type'] : $this->_defaultDriverType;
        if (class_exists($type)) {
            $class = $type;
        } else {
            $class = $this->_defaultDriverPrefix . ucfirst($type);
            if (!class_exists($class)) {
                throw new \InvalidArgumentException(
                    sprintf("Cannot create profiler driver, class \"%s\" doesn't exist.", $class
                ));
            }
        }
        $driver = new $class($config);
        if (!$driver instanceof \Magento\Profiler\DriverInterface) {
            throw new \InvalidArgumentException(sprintf(
                "Driver class \"%s\" must implement \Magento\Profiler\DriverInterface.", get_class($driver)
            ));
        }
        return $driver;
    }
}
