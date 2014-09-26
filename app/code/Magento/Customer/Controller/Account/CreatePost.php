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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\StoreManagerInterface;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Helper\Address;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatePost extends \Magento\Customer\Controller\Account
{
    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var CustomerAccountServiceInterface  */
    protected $customerAccountService;

    /** @var Address */
    protected $addressHelper;

    /** @var \Magento\Customer\Model\CustomerExtractor */
    protected $customerExtractor;

    /** @var FormFactory */
    protected $_formFactory;

    /** @var \Magento\Newsletter\Model\SubscriberFactory */
    protected $_subscriberFactory;

    /** @var \Magento\Customer\Service\V1\Data\RegionBuilder */
    protected $_regionBuilder;

    /** @var \Magento\Customer\Service\V1\Data\AddressBuilder */
    protected $_addressBuilder;

    /** @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder */
    protected $_customerDetailsBuilder;

    /** @var \Magento\Customer\Helper\Data */
    protected $_customerHelperData;

    /** @var \Magento\Framework\Escaper */
    protected $escaper;

    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param Address $addressHelper
     * @param UrlFactory $urlFactory
     * @param FormFactory $formFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Service\V1\Data\RegionBuilder $regionBuilder
     * @param \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder
     * @param \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder
     * @param \Magento\Customer\Helper\Data $customerHelperData
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Customer\Model\CustomerExtractor $customerExtractor
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CustomerAccountServiceInterface $customerAccountService,
        Address $addressHelper,
        UrlFactory $urlFactory,
        FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Service\V1\Data\RegionBuilder $regionBuilder,
        \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder,
        \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder,
        \Magento\Customer\Helper\Data $customerHelperData,
        \Magento\Framework\Escaper $escaper,
        \Magento\Customer\Model\CustomerExtractor $customerExtractor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerAccountService = $customerAccountService;
        $this->addressHelper = $addressHelper;
        $this->_formFactory = $formFactory;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_regionBuilder = $regionBuilder;
        $this->_addressBuilder = $addressBuilder;
        $this->_customerDetailsBuilder = $customerDetailsBuilder;
        $this->_customerHelperData = $customerHelperData;
        $this->escaper = $escaper;
        $this->customerExtractor = $customerExtractor;
        $this->urlModel = $urlFactory->create();
        parent::__construct($context, $customerSession);
    }

    /**
     * Add address to customer during create account
     *
     * @return \Magento\Customer\Service\V1\Data\Address|null
     */
    protected function _extractAddress()
    {
        if (!$this->getRequest()->getPost('create_address')) {
            return null;
        }

        $addressForm = $this->_formFactory->create('customer_address', 'customer_register_address');
        $allowedAttributes = $addressForm->getAllowedAttributes();

        $addressData = array();

        foreach ($allowedAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = $this->getRequest()->getParam($attributeCode);
            if (is_null($value)) {
                continue;
            }
            switch ($attributeCode) {
                case 'region_id':
                    $this->_regionBuilder->setRegionId($value);
                    break;
                case 'region':
                    $this->_regionBuilder->setRegion($value);
                    break;
                default:
                    $addressData[$attributeCode] = $value;
            }
        }
        $this->_addressBuilder->populateWithArray($addressData);
        $this->_addressBuilder->setRegion($this->_regionBuilder->create());

        $this->_addressBuilder->setDefaultBilling(
            $this->getRequest()->getParam('default_billing', false)
        )->setDefaultShipping(
            $this->getRequest()->getParam('default_shipping', false)
        );
        return $this->_addressBuilder->create();
    }

    /**
     * Is registration allowed
     *
     * @return bool
     */
    protected function isRegistrationAllowed()
    {
        return $this->_customerHelperData->isRegistrationAllowed();
    }

    /**
     * Create customer account action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if ($this->_getSession()->isLoggedIn() || !$this->isRegistrationAllowed()) {
            $this->_redirect('*/*/');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $url = $this->urlModel->getUrl('*/*/create', array('_secure' => true));
            $this->getResponse()->setRedirect($this->_redirect->error($url));
            return;
        }

        $this->_session->regenerateId();

        try {
            $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
            $address = $this->_extractAddress();
            $addresses = is_null($address) ? array() : array($address);
            $password = $this->getRequest()->getParam('password');
            $redirectUrl = $this->_getSession()->getBeforeAuthUrl();
            $customerDetails = $this->_customerDetailsBuilder
                ->setCustomer($customer)
                ->setAddresses($addresses)
                ->create();
            $customer = $this->customerAccountService->createCustomer($customerDetails, $password, $redirectUrl);

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $this->_subscriberFactory->create()->subscribeCustomerById($customer->getId());
            }

            $this->_eventManager->dispatch(
                'customer_register_success',
                array('account_controller' => $this, 'customer' => $customer)
            );

            $confirmationStatus = $this->customerAccountService->getConfirmationStatus($customer->getId());
            if ($confirmationStatus === CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                $email = $this->_customerHelperData->getEmailConfirmationUrl($customer->getEmail());
                // @codingStandardsIgnoreStart
                $this->messageManager->addSuccess(
                    __(
                        'Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%1">click here</a>.',
                        $email
                    )
                );
                // @codingStandardsIgnoreEnd
                $url = $this->urlModel->getUrl('*/*/index', array('_secure' => true));
                $this->getResponse()->setRedirect($this->_redirect->success($url));
            } else {
                $this->_getSession()->setCustomerDataAsLoggedIn($customer);

                $this->messageManager->addSuccess($this->getSuccessMessage());
                $this->getResponse()->setRedirect($this->getSuccessRedirect());
            }
            return;
        } catch (StateException $e) {
            $url = $this->urlModel->getUrl('customer/account/forgotpassword');
            // @codingStandardsIgnoreStart
            $message = __(
                'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.',
                $url
            );
            // @codingStandardsIgnoreEnd
            $this->messageManager->addError($message);
        } catch (InputException $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError($this->escaper->escapeHtml($error->getMessage()));
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Cannot save the customer.'));
        }

        $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
        $defaultUrl = $this->urlModel->getUrl('*/*/create', array('_secure' => true));
        $this->getResponse()->setRedirect($this->_redirect->error($defaultUrl));
    }

    /**
     * Retrieve success message
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        if ($this->addressHelper->isVatValidationEnabled()) {
            if ($this->addressHelper->getTaxCalculationAddressType() == Address::TYPE_SHIPPING) {
                // @codingStandardsIgnoreStart
                $message = __(
                    'If you are a registered VAT customer, please click <a href="%1">here</a> to enter you shipping address for proper VAT calculation',
                    $this->urlModel->getUrl('customer/address/edit')
                );
                // @codingStandardsIgnoreEnd
            } else {
                // @codingStandardsIgnoreStart
                $message = __(
                    'If you are a registered VAT customer, please click <a href="%1">here</a> to enter you billing address for proper VAT calculation',
                    $this->urlModel->getUrl('customer/address/edit')
                );
                // @codingStandardsIgnoreEnd
            }
        } else {
            $message = __('Thank you for registering with %1.', $this->storeManager->getStore()->getFrontendName());
        }
        return $message;
    }

    /**
     * Retrieve success redirect URL
     *
     * @return string
     */
    protected function getSuccessRedirect()
    {
        if (!$this->scopeConfig->isSetFlag(
                \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) && $this->_getSession()->getBeforeAuthUrl()
        ) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        } else {
            $successUrl = $this->urlModel->getUrl('*/*/index', array('_secure' => true));
        }
        return $this->_redirect->success($successUrl);
    }
}
