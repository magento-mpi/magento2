<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Api\Data\CustomerDataBuilder;

class Edit extends \Magento\Customer\Controller\Account
{
    /** @var CustomerAccountServiceInterface  */
    protected $customerAccountService;

    /** @var CustomerDataBuilder */
    protected $customerBuilder;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param CustomerDataBuilder $customerBuilder
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerAccountServiceInterface $customerAccountService,
        CustomerDataBuilder $customerBuilder
    ) {
        $this->customerAccountService = $customerAccountService;
        $this->customerBuilder = $customerBuilder;
        parent::__construct($context, $customerSession);
    }

    /**
     * Forgot customer account information page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

        $block = $this->_view->getLayout()->getBlock('customer_edit');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $data = $this->_getSession()->getCustomerFormData(true);
        $customerId = $this->_getSession()->getCustomerId();
        $customerDataObject = $this->customerAccountService->getCustomer($customerId);
        if (!empty($data)) {
            $customerDataObject = $this->customerBuilder->mergeDataObjectWithArray($customerDataObject, $data);
        }
        $this->_getSession()->setCustomerData($customerDataObject);
        $this->_getSession()->setChangePassword($this->getRequest()->getParam('changepass') == 1);

        $this->_view->getPage()->getConfig()->setTitle(__('Account Information'));
        $this->_view->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->_view->renderLayout();
    }
}
