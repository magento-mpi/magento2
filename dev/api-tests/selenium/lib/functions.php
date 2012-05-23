<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */
if (!function_exists('array_replace_recursive')) {
    function array_replace_recursive()
    {
        $args = func_get_args();
        $result = $args[0];
        if (!is_array($result)) {
            return $result;
        }
        for ($i = 1; $i < count($args); $i++) {
            if (!is_array($args[$i])) {
                continue;
            }
            foreach ($args[$i] as $key => $value) {
                if (!isset($result[$key]) || (isset($result[$key]) && !is_array($result[$key]))) {
                    $result[$key] = array();
                }
                if (is_array($value)) {
                    $value = array_replace_recursive($result[$key], $value);
                }
                $result[$key] = $value;
            }
        }
        return $result;
    }
}