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
 * Recent Customer Reviews Block
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Review_Block_Customer_Recent extends Mage_Core_Block_Template
{

    protected $_template = 'customer/list.phtml';

    protected function _construct()
    {
        parent::_construct();


        $this->_collection = Mage::getModel('Mage_Review_Model_Review')->getProductCollection();

        $this->_collection
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerId())
            ->setDateOrder()
            ->setPageSize(5)
            ->load()
            ->addReviewSummary();
    }

    public function count()
    {
        return $this->_collection->getSize();
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

    public function getAllReviewsUrl()
    {
        return Mage::getUrl('review/customer');
    }

    public function getReviewUrl($id)
    {
        return Mage::getUrl('review/customer/view', array('id' => $id));
    }
}
