<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * List of tagged products
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Block_Product_Result extends Magento_Catalog_Block_Product_Abstract
{
    protected $_productCollection;


    public function getTag()
    {
        return Mage::registry('current_tag');
    }

    protected function _prepareLayout()
    {
        $title = $this->getHeaderText();
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->getLayout()->getBlock('root')->setHeaderTitle($title);
        return parent::_prepareLayout();
    }

    public function setListOrders() {
        $this->getChildBlock('search_result_list')
            ->setAvailableOrders(array(
                'name' => __('Name'),
                'price'=>__('Price'))
            );
    }

    public function setListModes() {
        $this->getChildBlock('search_result_list')
            ->setModes(array(
                'grid' => __('Grid'),
                'list' => __('List'))
            );
    }

    public function setListCollection() {
        $this->getChildBlock('search_result_list')
           ->setCollection($this->_getProductCollection());
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    protected function _getProductCollection()
    {
        if(is_null($this->_productCollection)) {
            $tagModel = Mage::getModel('Magento_Tag_Model_Tag');
            $this->_productCollection = $tagModel->getEntityCollection()
                ->addAttributeToSelect(Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes())
                ->addTagFilter($this->getTag()->getId())
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addMinimalPrice()
                ->addUrlRewrite()
                ->setActiveFilter();
            $this->_productCollection->setVisibility(
                Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInSiteIds()
            );
        }

        return $this->_productCollection;
    }

    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getProductCollection()->getSize();
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    public function getHeaderText()
    {
        if( $this->getTag()->getName() ) {
            return __("Products tagged with '%1'", $this->escapeHtml($this->getTag()->getName()));
        } else {
            return false;
        }
    }

    public function getSubheaderText()
    {
        return false;
    }

    public function getNoResultText()
    {
        return __('We didn\'t find any matches.');
    }
}
