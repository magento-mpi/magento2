<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Detailed Product Reviews
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_Block_List_Detailed extends Mage_Catalog_Block_Product_View
{
    protected $_collection;

    public function __construct()
    {
        $this->setTemplate('review/product/detailed.phtml');
        $this->setProductId(Mage::registry('productId'));
    }

    public function toHtml()
    {
        $productId = $this->getProductId();
        if(!$product = Mage::registry('product')) {
        	$storeId = (int) Mage::getSingleton('core/store')->getId();
        	$product = Mage::getModel('catalog/product')
        		->setStoreId($storeId)
            	->load($productId)
            	->setStoreId($storeId);

           	Mage::register('product', $product);
        }

        $this->assign('customerIsLogin', Mage::getSingleton('customer/session')->isLoggedIn());
        $this->assign('reviewLink', Mage::getUrl('review/product/list', array('id'=>$productId)));
        $this->assign('wishlistLink', Mage::getUrl('wishlist/index/add', array('product'=>$productId)));

        $this->setChild('rating', $this->getLayout()->createBlock('rating/entity_detailed')
            ->setEntityId($productId));
        $this->setChild('reviewForm', $this->getLayout()->createBlock('review/form'));
        $this->setChild('reviewList', $this->getLayout()->createBlock('review/list', 'review_list'));
        $this->assign('reviewCount', $this->getLayout()->getBlock('review_list')->count());

        return parent::toHtml();
    }

    public function count()
    {
        return $this->getCollection()->getSize();
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'detailed_review_list.toolbar')
            ->setCollection($this->_getCollection());

        $this->setChild('toolbar', $toolbar);
        return $this;
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    protected function _getCollection()
    {
        if( !$this->_collection ) {
            $this->_collection = Mage::getModel('review/review')->getCollection();

            $this->_collection
                ->addStoreFilter(Mage::getSingleton('core/store')->getId())
                ->addEntityFilter('product', $this->getProductId())
                ->addStatusFilter('approved')
                ->setDateOrder();
        }
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }

    protected function _beforeToHtml()
    {
        $this->_getCollection()
            ->load()
            ->addRateVotes();
        return parent::_beforeToHtml();
    }

    public function getReviewUrl($id)
    {
        return Mage::getUrl('*/*/view', array('id' => $id));
    }
}