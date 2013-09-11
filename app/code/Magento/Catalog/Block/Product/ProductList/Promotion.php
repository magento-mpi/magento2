<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\ProductList;

class Promotion extends \Magento\Catalog\Block\Product\ListProduct
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = \Mage::getResourceModel('\Magento\Catalog\Model\Resource\Product\Collection');
            \Mage::getModel('\Magento\Catalog\Model\Layer')->prepareProductCollection($collection);
// your custom filter
            $collection->addAttributeToFilter('promotion', 1)
                ->addStoreFilter();

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
