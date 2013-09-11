<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product random items block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Catalog\Block\Product\ProductList;

class Random extends \Magento\Catalog\Block\Product\ListProduct
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Collection');
            \Mage::getModel('Magento\Catalog\Model\Layer')->prepareProductCollection($collection);
            $collection->getSelect()->order('rand()');
            $collection->addStoreFilter();
            $numProducts = $this->getNumProducts() ? $this->getNumProducts() : 0;
            $collection->setPage(1, $numProducts);

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
