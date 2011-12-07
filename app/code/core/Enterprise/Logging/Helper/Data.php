<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Logging helper
 */
class Enterprise_Logging_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Join array into string except empty values
     *
     * @param array $array Array to join
     * @param string $glue Separator to join
     * @return string
     */
    public function implodeValues($array, $glue = ', ')
    {
        if (!is_array($array)) {
            return $array;
        }
        $result = array();
        foreach ($array as $item) {
            if (is_array($item)) {
                $result[] = $this->implodeValues($item);
            }
            else {
                if ((string)$item !== '') {
                    $result[] = $item;
                }
            }
        }
        return implode($glue, $result);
    }
}
