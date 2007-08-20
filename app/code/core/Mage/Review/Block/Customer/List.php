<?php
/**
 * Customer Reviews list block
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_Block_Customer_List extends Mage_Core_Block_Template
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('review/customer/list.phtml');

        $this->_collection = Mage::getModel('review/review')->getProductCollection();

        $this->_collection
            #->addStoreFilter(Mage::getSingleton('core/store')->getId())
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
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

    protected function _initChildren()
    {
        $toolbar = $this->getLayout()->createBlock('catalog/product_list_toolbar', 'customer_review_list.toolbar')
            ->disableExpanded()
            ->setCollection($this->_getCollection());

        $this->setChild('toolbar', $toolbar);
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
        return Mage::getUrl('customer/review/view/');
    }

    public function getProductLink()
    {
        return Mage::getUrl('catalog/product/view/');
    }

    public function dateFormat($date)
    {
         return strftime(Mage::getStoreConfig('general/local/date_format_short'), strtotime($date));
    }

    protected function _beforeToHtml()
    {
        $this->_getCollection()
            ->load()
            ->addReviewSummary();
        return parent::_beforeToHtml();
    }
}