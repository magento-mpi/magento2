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
 * Tags Customer Reviews Block
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Block_Customer_Recent extends Magento_Core_Block_Template
{
    protected $_collection;

    protected function _initCollection()
    {
        $this->_collection = Mage::getModel('Magento_Tag_Model_Tag')->getEntityCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
            ->addAttributeToSelect(Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes())
            ->setDescOrder()
            ->setPageSize(5)
            ->setActiveFilter()
            ->load()
            ->addProductTags()
            ->setVisibility(Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInSiteIds());
    }

    public function count()
    {
        return $this->_getCollection()->getSize();
    }

    protected function _getCollection()
    {
        if (!$this->_collection) {
            $this->_initCollection();
        }
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }

    public function dateFormat($date)
    {
        return $this->formatDate($date, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
    }

    public function getAllTagsUrl()
    {
        return Mage::getUrl('tag/customer');
    }

    protected function _toHtml()
    {
        if ($this->_getCollection()->getSize() > 0) {
            return parent::_toHtml();
        }
        return '';
    }

}
