<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Invitation\Controller\Customer\Account;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Helper\Address;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\Data\RegionDataBuilder;
use Magento\Customer\Api\Data\AddressDataBuilder;
use Magento\Customer\Api\Data\CustomerDataBuilder;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Registration;
use Magento\Framework\Escaper;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Invitation\Model\InvitationProvider;
use Magento\Invitation\Model\Invitation;
use Magento\Framework\Model\Exception as FrameworkException;
use Magento\Store\Model\ScopeInterface;

class CreatePost extends \Magento\Customer\Controller\Account\CreatePost
{
    /**
     * @var InvitationProvider
     */
    protected $invitationProvider;

    /**
     * @var Registration
     */
    protected $registration;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $accountManagement
     * @param Address $addressHelper
     * @param UrlFactory $urlFactory
     * @param FormFactory $formFactory
     * @param SubscriberFactory $subscriberFactory
     * @param RegionDataBuilder $regionBuilder
     * @param AddressDataBuilder $addressBuilder
     * @param CustomerDataBuilder $customerDetailsBuilder
     * @param CustomerUrl $customerUrl
     * @param Escaper $escaper
     * @param CustomerExtractor $customerExtractor
     * @param InvitationProvider $invitationProvider
     * @param Registration $registration
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        Address $addressHelper,
        UrlFactory $urlFactory,
        FormFactory $formFactory,
        SubscriberFactory $subscriberFactory,
        RegionDataBuilder $regionBuilder,
        AddressDataBuilder $addressBuilder,
        CustomerDataBuilder $customerDetailsBuilder,
        CustomerUrl $customerUrl,
        Registration $registration,
        Escaper $escaper,
        CustomerExtractor $customerExtractor,
        InvitationProvider $invitationProvider
    ) {
        $this->invitationProvider = $invitationProvider;
        parent::__construct(
            $context,
            $customerSession,
            $scopeConfig,
            $storeManager,
            $accountManagement,
            $addressHelper,
            $urlFactory,
            $formFactory,
            $subscriberFactory,
            $regionBuilder,
            $addressBuilder,
            $customerDetailsBuilder,
            $customerUrl,
            $registration,
            $escaper,
            $customerExtractor
        );
    }

    /**
     * Create customer account action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $invitation = $this->invitationProvider->get($this->getRequest());

            parent::execute();

            $customerId = $this->_getSession()->getCustomerId();
            if ($customerId) {
                $invitation->accept($this->storeManager->getWebsite()->getId(), $customerId);
            }

            $this->_redirect('customer/account/');
            return;
        } catch (FrameworkException $e) {
            $_definedErrorCodes = [
                Invitation::ERROR_CUSTOMER_EXISTS,
                Invitation::ERROR_INVALID_DATA
            ];
            if (in_array($e->getCode(), $_definedErrorCodes)) {
                $this->messageManager->addError($e->getMessage())->setCustomerFormData($this->getRequest()->getPost());
            } else {
                if ($this->registration->isAllowed()) {
                    $this->messageManager->addError(__('Your invitation is not valid. Please create an account.'));
                    $this->_redirect('customer/account/create');
                    return;
                } else {
                    $this->messageManager->addError(
                        __(
                            'Your invitation is not valid. Please contact us at %1.',
                            $this->scopeConfig->getValue(
                                'trans_email/ident_support/email',
                                ScopeInterface::SCOPE_STORE
                            )
                        )
                    );
                    $this->_redirect('customer/account/login');
                    return;
                }
            }
        } catch (\Exception $e) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
            $this->messageManager->addException($e, __('Unable to save the customer.'));
        }

        $this->_redirect('magento_invitation/customer_account/create', ['_current' => true, '_secure' => true]);
    }
}
