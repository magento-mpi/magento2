<?php
/**
 * Abstract attribute property mapper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Setup;

abstract class PropertyMapperAbstract implements PropertyMapperInterface
{
    /**
     * Retrieve value from array by key or return default value
     *
     * @param array $array
     * @param string $key
     * @param string $default
     * @return string
     */
    protected function _getValue($array, $key, $default = null)
    {
        if (isset($array[$key]) && is_bool($array[$key])) {
            $array[$key] = (int) $array[$key];
        }
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
