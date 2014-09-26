<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Customer\Account;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\StoreManagerInterface;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Helper\Address;
use Magento\Framework\UrlFactory;

class CreatePost extends \Magento\Customer\Controller\Account\CreatePost
{
    /**
     * @var \Magento\Invitation\Model\InvitationProvider
     */
    protected $invitationProvider;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param Address $addressHelper
     * @param UrlFactory $urlFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Customer\Service\V1\Data\RegionBuilder $regionBuilder
     * @param \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder
     * @param \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder
     * @param \Magento\Customer\Helper\Data $customerHelperData
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Customer\Model\CustomerExtractor $customerExtractor
     * @param \Magento\Invitation\Model\InvitationProvider $invitationProvider
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CustomerAccountServiceInterface $customerAccountService,
        Address $addressHelper,
        UrlFactory $urlFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Service\V1\Data\RegionBuilder $regionBuilder,
        \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder,
        \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder,
        \Magento\Customer\Helper\Data $customerHelperData,
        \Magento\Framework\Escaper $escaper,
        \Magento\Customer\Model\CustomerExtractor $customerExtractor,
        \Magento\Invitation\Model\InvitationProvider $invitationProvider
    ) {
        $this->invitationProvider = $invitationProvider;
        parent::__construct(
            $context,
            $customerSession,
            $scopeConfig,
            $storeManager,
            $customerAccountService,
            $addressHelper,
            $urlFactory,
            $formFactory,
            $subscriberFactory,
            $regionBuilder,
            $addressBuilder,
            $customerDetailsBuilder,
            $customerHelperData,
            $escaper,
            $customerExtractor
        );
    }

    /**
     * Is registration allowed
     *
     * @return bool
     */
    protected function isRegistrationAllowed()
    {
        return true;
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
        } catch (\Magento\Framework\Model\Exception $e) {
            $_definedErrorCodes = array(
                \Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS,
                \Magento\Invitation\Model\Invitation::ERROR_INVALID_DATA
            );
            if (in_array($e->getCode(), $_definedErrorCodes)) {
                $this->messageManager->addError($e->getMessage())->setCustomerFormData($this->getRequest()->getPost());
            } else {
                if ($this->_customerHelperData->isRegistrationAllowed()) {
                    $this->messageManager->addError(__('Your invitation is not valid. Please create an account.'));
                    $this->_redirect('customer/account/create');
                    return;
                } else {
                    $this->messageManager->addError(
                        __(
                            'Your invitation is not valid. Please contact us at %1.',
                            $this->scopeConfig->getValue(
                                'trans_email/ident_support/email',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
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

        $this->_redirect('magento_invitation/customer_account/create', array('_current' => true, '_secure' => true));
    }
}
