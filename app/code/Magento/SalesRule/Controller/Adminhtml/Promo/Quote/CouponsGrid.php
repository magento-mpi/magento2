<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Controller\Adminhtml\Promo\Quote;

class CouponsGrid extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    /**
     * Coupon codes grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initRule();
        $this->_view->loadLayout()->renderLayout();
    }
}
