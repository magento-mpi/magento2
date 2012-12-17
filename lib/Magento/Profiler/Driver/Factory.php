<?php
/**
 * Profiler driver factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Factory
{
    /**
     * Default driver class prefix
     */
    const DRIVER_CLASS_PREFIX = 'Magento_Profiler_Driver_';

    /**
     * Default driver type
     */
    const DEFAULT_DRIVER_TYPE = 'standard';

    /**
     * Create instance of profiler driver
     *
     * @param Magento_Profiler_Driver_Configuration $config
     * @throws InvalidArgumentException
     * @return Magento_Profiler_DriverInterface
     */
    public function create(Magento_Profiler_Driver_Configuration $config)
    {
        $type = $config->getDriverTypeValue(self::DEFAULT_DRIVER_TYPE);
        if (class_exists($type)) {
            $class = $type;
        } else {
            $class = self::DRIVER_CLASS_PREFIX . ucfirst($type);
            if (!class_exists($class)) {
                throw new InvalidArgumentException(
                    sprintf("Cannot create profiler driver, class \"%s\" doesn't exist.", $class
                ));
            }
        }
        $driver = new $class($config);
        if (!$driver instanceof Magento_Profiler_DriverInterface) {
            throw new InvalidArgumentException(sprintf(
                "Driver class \"%s\" must implement Magento_Profiler_DriverInterface.", get_class($driver)
            ));
        }
        return $driver;
    }
}
