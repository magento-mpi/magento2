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
 * Catalog product related items block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_List_Related extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    protected $_itemCollection;

    protected function _prepareData()
    {
        $product = $this->_coreRegistry->registry('product');
        /* @var $product Magento_Catalog_Model_Product */

        $this->_itemCollection = $product->getRelatedProductCollection()
            ->addAttributeToSelect('required_options')
            ->setPositionOrder()
            ->addStoreFilter();

        if (Mage::helper('Magento_Catalog_Helper_Data')->isModuleEnabled('Magento_Checkout')) {
            Mage::getResourceSingleton('Magento_Checkout_Model_Resource_Cart')
                ->addExcludeProductFilter(
                    $this->_itemCollection,
                    Mage::getSingleton('Magento_Checkout_Model_Session')->getQuoteId()
            );
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility(
            Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInCatalogIds()
        );

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        return $this->_itemCollection;
    }
}
