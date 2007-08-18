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

    public function getPagerHtml()
    {
        if( $this->getUsePager() ) {
            return $this->getChildHtml('pager');
        } else {
            $this->getChildHtml('pager');
        }
    }

    protected function _initChildren()
    {
        $this->setChild('pager',
            $this->getLayout()->createBlock('page/html_pager', 'pager')
                        ->setCollection($this->_getCollection())
                        ->setUrlPrefix('customer')
                        ->setViewBy('limit')
                        ->setParam('limit', 10)
        );
    }

    protected function _getCollection()
    {
        return $this->_collection;
    }

    public function getCollection()
    {
        $this->_getCollection()
            ->addReviewSummary();
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
}