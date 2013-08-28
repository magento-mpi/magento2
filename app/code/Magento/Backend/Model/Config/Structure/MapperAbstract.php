<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Converter Mapper Interface
 */
abstract class Magento_Backend_Model_Config_Structure_MapperAbstract
    implements Magento_Backend_Model_Config_Structure_MapperInterface
{
    /**
     * Check value existence
     *
     * @param string $key
     * @param array $target
     * @return bool
     */
    protected function _hasValue($key, $target)
    {
        if (false == is_array($target)) {
            return false;
        }

        $paths = explode('/', $key);
        foreach ($paths as $path) {
            if (array_key_exists($path, $target)) {
                $target = $target[$path];
            } else {
                return false;
            }
        }
        return true;
    }
}
