<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist;

class Index extends \Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist
{
    /**
     * Index Action.
     * Forward to Wishlist Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('wishlist');
    }
}
