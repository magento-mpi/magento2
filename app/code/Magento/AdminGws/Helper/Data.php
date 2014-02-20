<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Helper;

/**
 * Admin GWS helper
 *
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Transform comma-separeated ids string into array
     *
     * @param mixed $ids
     * @param string $separator
     * @return array
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
