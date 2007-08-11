<?php
/**
 * Detailed Product Reviews
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_Block_List_Detailed extends Mage_Core_Block_Template
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

        $this->assign('product', $product);
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
            $this->getLayout()->createBlock('page/html_pager', '_pager')
                        ->setCollection($this->_getCollection())
                        ->setUrlPrefix('review')
                        ->setViewBy('limit')
                        ->setViewBy('order', array('date', 'rating'))
        );
    }

    protected function _getCollection()
    {
        if( !$this->_collection ) {
            $this->_collection = Mage::getModel('review/review')->getCollection();

            $this->_collection
                ->addStoreFilter(Mage::getSingleton('core/store')->getId())
                ->addEntityFilter('product', $this->getProductId())
                ->setDateOrder();
        }
        return $this->_collection;
    }

    public function getCollection()
    {
        $this->_getCollection()
            ->addRateVotes();
        return $this->_getCollection();
    }

    public function getReviewUrl($id)
    {
        return Mage::getUrl('*/*/view', array('id' => $id));
    }
}