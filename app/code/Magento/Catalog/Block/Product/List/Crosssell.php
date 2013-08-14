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
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Catalog_Block_Product_List_Crosssell extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Crosssell item collection
     *
     * @var Magento_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    protected $_itemCollection;

    /**
     * Prepare crosssell items data
     *
     * @return Magento_Catalog_Block_Product_List_Crosssell
     */
    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Magento_Catalog_Model_Product */

        $this->_itemCollection = $product->getCrossSellProductCollection()
            ->addAttributeToSelect(Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes())
            ->setPositionOrder()
            ->addStoreFilter();

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    /**
     * Before rendering html process
     * Prepare items collection
     *
     * @return Magento_Catalog_Block_Product_List_Crosssell
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve crosssell items collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getItems()
    {
        return $this->_itemCollection;
    }

}
