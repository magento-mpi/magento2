<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Reviews list block
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Block_Customer_List extends Mage_Customer_Block_Account_Dashboard
{

    protected $_collection;

    protected function _construct()
    {
        $this->_collection = Mage::getModel('Mage_Review_Model_Review')->getProductCollection();
        $this->_collection
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerId())
            ->setDateOrder();
    }

    public function count()
    {
        return $this->_collection->getSize();
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('Mage_Page_Block_Html_Pager', 'customer_review_list.toolbar')
            ->setCollection($this->_getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    protected function _getCollection()
    {
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }

    public function getReviewLink()
    {
        return Mage::getUrl('review/customer/view/');
    }

    public function getProductLink()
    {
        return Mage::getUrl('catalog/product/view/');
    }

    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    protected function _beforeToHtml()
    {
        $this->_getCollection()
            ->load()
            ->addReviewSummary();
        return parent::_beforeToHtml();
    }

}
