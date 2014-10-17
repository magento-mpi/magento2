<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Controller\Search;

use \Magento\Framework\App\Action\NotFoundException;

class View extends \Magento\MultipleWishlist\Controller\Search
{
    /**
     * View customer wishlist
     *
     * @return void
     * @throws NotFoundException
     */
    public function execute()
    {
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        if (!$wishlistId) {
            throw new NotFoundException();
        }
        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->_wishlistFactory->create();
        $wishlist->load($wishlistId);
        if (
            !$wishlist->getId()
            || !$wishlist->getVisibility()
            && $wishlist->getCustomerId() != $this->_customerSession->getCustomerId()
        ) {
            throw new NotFoundException();
        }
        $this->_coreRegistry->register('shared_wishlist', $wishlist);
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->getBlock('customer.wishlist.info');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
