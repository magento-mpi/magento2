<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObject;

/**
 * TaxRuleSearchResults Service Data Object used for the search service requests
 */
class TaxRuleSearchResults extends AbstractObject
{
    /**
     * Get items
     *
     * @return \Magento\Tax\Service\V1\Data\TaxRule[]
     */
    public function getItems()
    {
        return is_null($this->_get(self::KEY_ITEMS)) ? [] : $this->_get(self::KEY_ITEMS);
    }
}
