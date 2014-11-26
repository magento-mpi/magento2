<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Code\Generator;

use Magento\Framework\Autoload\AutoloaderRegistry;

/**
 * DefinedClasses class detects if a class has been defined
 */
class DefinedClasses
{

    /**
     * Determine if a class can be loaded without using the Code\Generator\Autoloader.
     *
     * @param string $className
     * @return bool
     */
    public function classLoadable($className)
    {
        if (class_exists($className, false) || interface_exists($className, false)) {
            return true;
        }
        try {
            return AutoloaderRegistry::getAutoloader()->loadClass($className);
        } catch (\Exception $e) {
            // Couldn't get access to the autoloader so we need to allow class_exists to call autoloader chain
            return (class_exists($className) || interface_exists($className));
        }
    }
}
