<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
