<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Product collection resource
 *
 */
namespace Magento\AdvancedCheckout\Model\Resource\Product;

class Collection extends \Magento\Catalog\Model\Resource\Product\Collection
{
    /**
     * Join Product Price Table using left-join
     *
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected function _productLimitationJoinPrice()
    {
        return $this->_productLimitationPrice(true);
    }
}
