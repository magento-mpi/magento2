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
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist block customer items
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Block_Customer_Wishlist extends Mage_Core_Block_Template
{
    protected $_wishlistLoaded = false;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('wishlist/view.phtml');
        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle(__('My Wishlist'));
    }

    public function getWishlist()
    {
        if(!$this->_wishlistLoaded) {
            Mage::registry('wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
            Mage::registry('wishlist')->getProductCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect('status')
                ->addAttributeToFilter('store_id', array('in'=>Mage::registry('wishlist')->getSharedStoreIds()))
                ->load();

            $this->_wishlistLoaded = true;
        }

        return Mage::registry('wishlist')->getProductCollection();
    }

    public function getEscapedDescription(Varien_Object $item)
    {
        return $this->htmlEscape($item->getDescription());
    }

    public function getFormatedDate($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    public function getItemAddToCartUrl($item)
    {
        return $this->getUrl('*/*/cart',array('item'=>$item->getWishlistItemId()));
    }

    public function getItemRemoveUrl($item)
    {
        return $this->getUrl('*/*/remove',array('item'=>$item->getWishlistItemId()));
    }
}// Class Mage_Wishlist_Block_Customer_Wishlist END
