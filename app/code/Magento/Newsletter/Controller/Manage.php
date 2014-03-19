<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller;

use Magento\App\RequestInterface;

/**
 * Customers newsletter subscription controller
 */
class Manage extends \Magento\App\Action\Action
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder
     */
    protected $_customerDetailsBuilder;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder
     */
    protected $_customerBuilder;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder
     * @param \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        $this->_formKeyValidator = $formKeyValidator;
        $this->_customerSession = $customerSession;
        $this->_customerAccountService = $customerAccountService;
        $this->_customerDetailsBuilder = $customerDetailsBuilder;
        $this->_customerBuilder = $customerBuilder;
        $this->_subscriberFactory = $subscriberFactory;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_customerSession->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Managing newsletter subscription page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

        if ($block = $this->_view->getLayout()->getBlock('customer_newsletter')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->getLayout()->getBlock('head')->setTitle(__('Newsletter Subscription'));
        $this->_view->renderLayout();
    }

    /**
     * Save newsletter subscription preference action
     *
     * @return void|null
     */
    public function saveAction()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('customer/account/');
        }

        $customerId = $this->_customerSession->getCustomerId();
        if (is_null($customerId)) {
            $this->messageManager->addError(__('Something went wrong while saving your subscription.'));
        } else {
            try {
                $customer = $this->_customerAccountService->getCustomer($customerId);
                $storeId = $this->_storeManager->getStore()->getId();
                $customerDetails = $this->_customerDetailsBuilder->setAddresses(null)
                    ->setCustomer($this->_customerBuilder->populate($customer)->setStoreId($storeId)->create())
                    ->create();
                $this->_customerAccountService->updateCustomer($customerDetails);

                if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                    $this->_subscriberFactory->create()->subscribeCustomerById($customerId);
                    $this->messageManager->addSuccess( __('We saved the subscription.'));
                } else {
                    $this->_subscriberFactory->create()->unsubscribeCustomerById($customerId);
                    $this->messageManager->addSuccess(__('We removed the subscription.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving your subscription.'));
            }
        }
        $this->_redirect('customer/account/');
    }
}
