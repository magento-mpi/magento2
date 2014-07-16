<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller\Index;

class Wishlist extends Nofeed
{
    /**
     * Wishlist rss feed action
     * Show all public wishlists and private wishlists that belong to current user
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_scopeConfig->getValue('rss/wishlist/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $wishlist = $this->_wishlistHelper->getWishlist();
            if ($wishlist && ($wishlist->getVisibility()
                || $this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)
                && $wishlist->getCustomerId() == $this->_wishlistHelper->getCustomer()->getId())
            ) {
                $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
                $this->_view->loadLayout(false);
                $this->_view->renderLayout();
                return;
            }
        }
        parent::execute();
    }
}
