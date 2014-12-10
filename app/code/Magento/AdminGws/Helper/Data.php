<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdminGws\Helper;

/**
 * Admin GWS helper
 *
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
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
            return [];
        }
        if (!is_array($ids)) {
            return explode($separator, $ids);
        }
        return $ids;
    }
}
