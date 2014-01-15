<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Customer;
use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\CustomerGroupServiceInterface;

/**
 * Invitation customer account frontend controller
 */
class Account extends \Magento\Customer\Controller\Account
{
    /**
     * Invitation Config
     *
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * Invitation Factory
     *
     * @var \Magento\Invitation\Model\InvitationFactory
     */
    protected $_invitationFactory;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\UrlFactory $urlFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\FormFactory $formFactory
     * @param \Magento\Stdlib\String $string
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Escaper $escaper
     * @param CustomerGroupServiceInterface $customerGroupService
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Service\V1\Dto\RegionBuilder $regionBuilder
     * @param \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder
     * @param \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder
     * @param \Magento\Invitation\Model\Config $config
     * @param \Magento\Invitation\Model\InvitationFactory $invitationFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\UrlFactory $urlFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\FormFactory $formFactory,
        \Magento\Stdlib\String $string,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Escaper $escaper,
        CustomerGroupServiceInterface $customerGroupService,
        CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Service\V1\Dto\RegionBuilder $regionBuilder,
        \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder,
        \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder,
        \Magento\Invitation\Model\Config $config,
        \Magento\Invitation\Model\InvitationFactory $invitationFactory
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $urlFactory,
            $customerSession,
            $customerFactory,
            $formFactory,
            $string,
            $formKeyValidator,
            $subscriberFactory,
            $storeManager,
            $escaper,
            $customerGroupService,
            $customerAccountService,
            $regionBuilder,
            $addressBuilder,
            $customerBuilder
        );
        $this->_config = $config;
        $this->_invitationFactory = $invitationFactory;
    }

    /**
     * Bypassing direct parent dispatch
     * Allowing only specific actions
     * Checking whether invitation functionality is enabled
     * Checking whether registration is allowed at all
     * No way to logged in customers
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     * @throws \Magento\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!preg_match('/^(create|createpost)/i', $request->getActionName()) || !$this->_config->isEnabledOnFront()) {
            throw new NotFoundException();
        }
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account/');
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return parent::dispatch($request);
        }
        return parent::dispatch($request);
    }

    /**
     * Initialize invitation from request
     *
     * @return \Magento\Invitation\Model\Invitation
     */
    protected function _initInvitation()
    {
        if (!$this->_coreRegistry->registry('current_invitation')) {
            $invitation = $this->_invitationFactory->create();
            $invitation
                ->loadByInvitationCode($this->_objectManager->get('Magento\Core\Helper\Data')->urlDecode(
                        $this->getRequest()->getParam('invitation', false)
                    ))
                ->makeSureCanBeAccepted();
            $this->_coreRegistry->register('current_invitation', $invitation);
        }
        return $this->_coreRegistry->registry('current_invitation');
    }

    /**
     * Customer register form page
     */
    public function createAction()
    {
        try {
            $this->_initInvitation();
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $this->_view->renderLayout();
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('customer/account/login');
    }

    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        try {
            $invitation = $this->_initInvitation();

            $customer = $this->_customerFactory->create()
                ->setId(null)->setSkipConfirmationIfEmail($invitation->getEmail());
            $this->_coreRegistry->register('current_customer', $customer);

            $groupId = $invitation->getGroupId();
            if ($groupId) {
                $customer->setGroupId($groupId);
            }

            parent::createPostAction();

            $customerId = $customer->getId();
            if ($customerId) {
                $invitation->accept($this->_storeManager->getWebsite()->getId(), $customerId);
            }
            $this->_redirect('customer/account/');
            return;
        } catch (\Magento\Core\Exception $e) {
            $_definedErrorCodes = array(
                \Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS,
                \Magento\Invitation\Model\Invitation::ERROR_INVALID_DATA
            );
            if (in_array($e->getCode(), $_definedErrorCodes)) {
                $this->messageManager->addError($e->getMessage())
                    ->setCustomerFormData($this->getRequest()->getPost());
            } else {
                if ($this->_objectManager->get('Magento\Customer\Helper\Data')->isRegistrationAllowed()) {
                    $this->messageManager->addError(
                        __('Your invitation is not valid. Please create an account.')
                    );
                    $this->_redirect('customer/account/create');
                    return;
                } else {
                    $this->messageManager->addError(__('Your invitation is not valid. Please contact us at %1.',
                            $this->_objectManager->get('Magento\Core\Model\Store\Config')
                                ->getConfig('trans_email/ident_support/email'))
                    );
                    $this->_redirect('customer/account/login');
                    return;
                }
            }
        } catch (\Exception $e) {
            $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
            $this->messageManager->addException($e, __('Unable to save the customer.'));
        }

        $this->_redirect('magento_invitation/customer_account/create',
            array('_current' => true, '_secure' => true));
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param mixed $key
     * @return bool|null
     * @throws \Exception
     */
    protected function _checkCustomerActive($customer, $key)
    {
        if ($customer->getConfirmation()) {
            if ($customer->getConfirmation() !== $key) {
                throw new \Exception(__('Wrong confirmation key.'));
            }
            $this->_activateCustomer($customer);

            // log in and send greeting email, then die happy
            $this->_getSession()->setCustomerAsLoggedIn($customer);
            $this->_redirect('customer/account/');
            return true;
        }
    }

    /**
     * Confirm customer account by id and confirmation key
     */
    public function confirmAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            $customerId = $this->getRequest()->getParam('id', false);
            $key     = $this->getRequest()->getParam('key', false);
            if (empty($customerId) || empty($key)) {
                throw new \Exception(__('Bad request.'));
            }

            $customer = $this->_loadCustomerById($customerId);
            if (true === $this->_checkCustomerActive($customer, $key)) {
                return;
            }
            // die happy
            $this->_redirect('customer/account/');
            return;
        } catch (\Exception $e) {
            // die unhappy
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('magento_invitation/customer_account/create',
                array('_current' => true, '_secure' => true));
            return;
        }
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param string $email
     */
    protected function _confirmByEmail($customer, $email)
    {
        try {
            $customer->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())->loadByEmail($email);
            if (!$customer->getId()) {
                throw new \Exception('');
            }
            if ($customer->getConfirmation()) {
                $customer->sendNewAccountEmail('confirmation', '', $this->_storeManager->getStore()->getId());
                $this->messageManager->addSuccess(__('Please, check your email for confirmation key.'));
            } else {
                $this->messageManager->addSuccess(__('This email does not require confirmation.'));
            }
            $this->_getSession()->setUsername($email);
            $this->_redirect('customer/account/');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Wrong email.'));
            $this->_redirect('magento_invitation/customer_account/create',
                array('_current' => true, '_secure' => true));
        }
    }

}
