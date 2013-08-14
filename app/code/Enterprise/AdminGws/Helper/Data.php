<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin GWS helper
 *
 */
class Enterprise_AdminGws_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Transform comma-separeated ids string into array
     *
     * @param mixed $ids
     * @return mixed
     */
    public function explodeIds($ids, $separator = ',')
    {
        if (empty($ids) && $ids !== 0 && $ids !== '0') {
            return array();
        }
        if (!is_array($ids)) {
            return explode($separator, $ids);
        }
        return $ids;
    }
}
