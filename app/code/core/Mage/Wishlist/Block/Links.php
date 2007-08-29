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
 * Links block
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Wishlist_Block_Links extends Mage_Core_Block_Template
{
    protected $_wishlist = null;

    public function addWishlistLink()
    {
        $count = $this->getWishlistItems()->getSize();
        if( $count > 1 ) {
            $text = __('My Wishlist (%d items)', $count);
        } elseif( $count == 1 ) {
            $text = __('My Wishlist (%d item)', $count);
        } else {
            $text = __('My Wishlist');
        }

        $this->getParentBlock()->addLink(null, 'href="'.Mage::getUrl('wishlist').'"', $text);
    }

	public function getWishlist()
	{
		if(is_null($this->_wishlist)) {
			$this->_wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());

			$this->_wishlist->getProductCollection()
				->addAttributeToSelect('name')
				->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image')
				->addAttributeToFilter('store_id', array('in'=>$this->_wishlist->getSharedStoreIds()))
				->addAttributeToSort('added_at', 'desc')
                ->setCurPage(1)
				->setPageSize(3)
				->load();
		}

		return $this->_wishlist;
	}

	public function getWishlistItems()
	{
		return $this->getWishlist()->getProductCollection();
	}
}