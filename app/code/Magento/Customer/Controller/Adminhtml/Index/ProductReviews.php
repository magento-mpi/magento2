<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Index;

use Magento\Customer\Controller\RegistryConstants;

class ProductReviews extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Get customer's product reviews list
     *
     * @return void
     */
    public function execute()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->prepareDefaultCustomerTitle();
        $this->_view->getLayout()->getBlock('admin.customer.reviews')->setCustomerId(
            $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
        )
            ->setUseAjax(true);
        $this->_view->renderLayout();
    }
}
