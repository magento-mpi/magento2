<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Account;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

class Edit extends \Magento\Customer\Controller\Account
{
    /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder */
    protected $_customerBuilder;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\State $appState
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $appState,
        CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
    ) {
        $this->_customerBuilder = $customerBuilder;
        parent::__construct(
            $context,
            $customerSession,
            $addressHelper,
            $urlFactory,
            $storeManager,
            $scopeConfig,
            $appState,
            $customerAccountService
        );
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
        $customerDataObject = $this->_customerAccountService->getCustomer($customerId);
        if (!empty($data)) {
            $customerDataObject = $this->_customerBuilder->mergeDataObjectWithArray($customerDataObject, $data);
        }
        $this->_getSession()->setCustomerData($customerDataObject);
        $this->_getSession()->setChangePassword($this->getRequest()->getParam('changepass') == 1);

        $this->_view->getLayout()->getBlock('head')->setTitle(__('Account Information'));
        $this->_view->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->_view->renderLayout();
    }
}
