<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Controller\Index;

class Index extends \Magento\Wishlist\Controller\Index\Index
{
    /**
     * Display customer wishlist
     *
     * @return void
     */
    public function execute()
    {
        /* @var $helper \Magento\MultipleWishlist\Helper\Data */
        $helper = $this->_objectManager->get('Magento\MultipleWishlist\Helper\Data');
        if (!$helper->isMultipleEnabled()) {
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId && $wishlistId != $helper->getDefaultWishlist()->getId()) {
                $this->getResponse()->setRedirect($helper->getListUrl());
            }
        }
        parent::execute();
    }
}
