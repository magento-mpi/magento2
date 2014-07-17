<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
