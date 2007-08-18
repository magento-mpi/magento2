<?php
/**
 * List of products tagged by customer
 *
 * @package     Mage
 * @subpackage  Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Block_Customer_View extends Mage_Core_Block_Template
{
    protected $_collection;
    protected $_tagInfo;

    public function __construct()
    {
        $this->setTemplate('tag/customer/view.phtml');
        $this->setTagId(Mage::registry('tagId'));
    }

    public function getTagInfo()
    {
        if( !$this->_tagInfo ) {
            $this->_tagInfo = Mage::getModel('tag/tag')->load($this->getTagId());
        }
        return $this->_tagInfo;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getMyProducts()
    {
        return $this->_getCollection()->getItems();
    }

    public function getCount()
    {
        return sizeof($this->getMyProducts());
    }

    public function getReviewUrl($productId)
    {
        return Mage::getUrl('review/product/list', array('id' => $productId));
    }

    protected function _initChildren()
    {
        $this->setChild('pager',
            $this->getLayout()->createBlock('page/html_pager', 'review_pager')
                        ->setCollection($this->_getCollection())
                        ->setUrlPrefix('tag')
                        ->setViewBy('limit')
                        ->setViewBy('order', array('name', 'price'))
        );
    }

    protected function _getCollection()
    {
        if( !$this->_collection ) {
            $tagModel = Mage::getModel('tag/tag');
            $this->_collection = $tagModel->getEntityCollection();

            $this->_collection
                #->addStoreFilter(Mage::getSingleton('core/store')->getId())
                ->addTagFilter($this->getTagId())
                #->addStatusFilter($tagModel->getApprovedStatus())
                ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
                ->addAttributeToSelect('description');
        }

        return $this->_collection;
    }
}