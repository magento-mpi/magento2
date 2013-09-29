<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin GWS helper
 *
 */
namespace Magento\AdminGws\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
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
