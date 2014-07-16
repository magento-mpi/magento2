<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist;

class Wishlist extends \Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist
{
    /**
     * Init layout and add breadcrumbs
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_MultipleWishlist::report_customers_wishlist'
        )->_addBreadcrumb(
            __('Reports'),
            __('Reports')
        )->_addBreadcrumb(
            __('Customers'),
            __('Customers')
        );
        return $this;
    }

    /**
     * Wishlist view action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__("Customer Wish List Report"));

        $this->_initAction();
        $this->_view->renderLayout();
    }
}
