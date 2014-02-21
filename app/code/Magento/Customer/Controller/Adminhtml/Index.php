<?php
/**
 * Customer admin controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml;

use Magento\App\Action\NotFoundException;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\Dto\Customer;
use Magento\Customer\Service\V1\Dto\CustomerBuilder;
use Magento\Customer\Service\V1\Dto\AddressBuilder;
use Magento\Customer\Service\V1\Dto\Address;
use Magento\Customer\Service\V1\CustomerServiceInterface;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;
use Magento\Exception\NoSuchEntityException;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Validator
     */
    protected $_validator;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory = null;

    /** @var  CustomerBuilder */
    protected $_customerBuilder;

    /** @var  AddressBuilder */
    protected $_addressBuilder;
    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory = null;

    /** @var \Magento\Newsletter\Model\SubscriberFactory */
    protected $_subscriberFactory;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_dataHelper = null;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_formFactory;

    /** @var  CustomerServiceInterface */
    protected $_customerService;

    /** @var CustomerAddressServiceInterface */
    protected $_addressService;

    /** @var  \Magento\Customer\Helper\View */
    protected $_viewHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerBuilder $customerBuilder
     * @param AddressBuilder $addressBuilder
     * @param CustomerServiceInterface $customerService
     * @param CustomerAddressServiceInterface $addressService
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\Customer\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerBuilder $customerBuilder,
        AddressBuilder $addressBuilder,
        CustomerServiceInterface $customerService,
        CustomerAddressServiceInterface $addressService,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Customer\Helper\Data $helper
    ) {
        $this->_fileFactory = $fileFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerFactory = $customerFactory;
        $this->_customerBuilder = $customerBuilder;
        $this->_addressBuilder = $addressBuilder;
        $this->_addressFactory = $addressFactory;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_dataHelper = $helper;
        $this->_formFactory = $formFactory;
        $this->_customerService = $customerService;
        $this->_addressService = $addressService;
        $this->_viewHelper = $viewHelper;
        parent::__construct($context);
    }

    /**
     * Customer initialization
     *
     * @param string $idFieldName
     * @return string customer id
     */
    protected function _initCustomer($idFieldName = 'id')
    {
        // Default title
        $this->_title->add(__('Customers'));

        $customerId = (int)$this->getRequest()->getParam($idFieldName);
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
        if ($customerId) {
            $customer->load($customerId);
        }

        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER, $customer);
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
        return $customerId;
    }

    /**
     * Customers list action
     */
    public function indexAction()
    {
        $this->_title->add(__('Customers'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->_view->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('Magento_Customer::customer_manage');

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->_view->getLayout()->createBlock('Magento\Customer\Block\Adminhtml\Customer', 'customer')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(__('Customers'), __('Customers'));
        $this->_addBreadcrumb(__('Manage Customers'), __('Manage Customers'));

        $this->_view->renderLayout();
    }

    /**
     * Customer grid action
     */
    public function gridAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Customer edit action
     */
    public function editAction()
    {
        $customerId = $this->_initCustomer();
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Customer::customer_manage');

        $customerData = [];
        $customerData['account'] = [];
        $customerData['address'] = [];
        $customer = null;
        if (!empty($customerId)) {
            try {
                $customer = $this->_customerService->getCustomer($customerId);
                $customerData['account'] = $customer->getAttributes();
                $customerData['account']['id'] = $customerId;
                try {
                    $addresses = $this->_addressService->getAddresses($customerId);
                    foreach ($addresses as $address) {
                        $customerData['address'][$address->getId()] = $address->getAttributes();
                        $customerData['address'][$address->getId()]['id'] = $address->getId();
                    }
                } catch (NoSuchEntityException $e) {
                    //do nothing
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException($e, __('An error occurred while editing the customer.'));
                $this->_redirect('customer/*/index');
                return;
            }
        }
        $customerData['customer_id'] = $customerId;

        // set entered data if was error when we do save
        $data = $this->_getSession()->getCustomerData(true);

        // restore data from SESSION
        if ($data && (
                !isset($data['customer_id']) ||
                (isset($data['customer_id']) && $data['customer_id'] == $customerId)
            )
        ) {
            $request = clone $this->getRequest();
            $request->setParams($data);

            if (isset($data['account'])) {
                $customerForm = $this->_formFactory->create(
                    'customer',
                    'adminhtml_customer',
                    $customerData['account'],
                    true
                );
                $formData = $customerForm->extractData($request, 'account');
                $customerData['account'] = $customerForm->restoreData($formData);
                $customer = $this->_customerBuilder->populateWithArray($customerData['account'])->create();
            }

            if (isset($data['address']) && is_array($data['address'])) {
                foreach (array_keys($data['address']) as $addressId) {
                    if ($addressId == '_template_') {
                        continue;
                    }

                    try {
                        $address = $this->_addressService->getAddressById($addressId);
                        if (!empty($customerId) && $address->getCustomerId() == $customerId) {
                            $this->_addressBuilder->populate($address);
                        }
                    } catch (NoSuchEntityException $e) {
                        $this->_addressBuilder->setId($addressId);
                    }
                    if (!empty($customerId)) {
                        $this->_addressBuilder->setCustomerId($customerId);
                    }
                    $this->_addressBuilder->setDefaultBilling(
                        !empty($data['account']['default_billing'])
                        && $data['account']['default_billing'] == $addressId
                    );
                    $this->_addressBuilder->setDefaultShipping(
                        !empty($data['account']['default_shipping'])
                        && $data['account']['default_shipping'] == $addressId
                    );
                    $address = $this->_addressBuilder->create();
                    $requestScope = sprintf('address/%s', $addressId);
                    $addressForm = $this->_formFactory->create(
                        'customer_address',
                        'adminhtml_customer_address',
                        $address->getAttributes()
                    );
                    $formData = $addressForm->extractData($request, $requestScope);
                    $customerData['address'][$addressId] = $addressForm->restoreData($formData);
                    $customerData['address'][$addressId]['id'] = $addressId;
                }
            }
        }

        $this->_getSession()->setCustomerData($customerData);

        if (is_null($customer)) {
            $this->_title->add(__('New Customer'));
        } else {
            $this->_title->add($this->_viewHelper->getCustomerName($customer));
        }
        /**
         * Set active menu item
         */
        $this->_setActiveMenu('Magento_Customer::customer');

        $this->_view->renderLayout();
    }

    /**
     * Create new customer action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Delete customer action
     */
    public function deleteAction()
    {
        $this->_initCustomer();
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        if (!empty($customerId)) {
            try {
                $this->_customerService->deleteCustomer($customerId);
                $this->messageManager->addSuccess(
                    __('You deleted the customer.'));
            } catch (\Exception $exception){
                $this->messageManager->addError($exception->getMessage());
            }
        }
        $this->_redirect('customer/index');
    }

    /**
     * Save customer action
     */
    public function saveAction()
    {
        $returnToEdit = false;
        $customerId = (int)$this->getRequest()->getPost('customer_id');
        $originalRequestData = $this->getRequest()->getPost();
        if ($originalRequestData) {
            try {
                // optional fields might be set in request for future processing by observers in other modules
                $customerData = $this->_extractCustomerData();
                $addressesData = $this->_extractCustomerAddressData();
                $request = $this->getRequest();
                $isExistingCustomer = (bool)$customerId;

                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = null;
                if ($isExistingCustomer) {
                    // load the customer from the db
                    $customer = $this->_loadCustomerById($customerId);
                } else {
                    // create a new customer
                    $customer = $this->_customerFactory->create();
                    // Need to set proper attribute id or future updates will cause data loss.
                    $customer->setData('attribute_set_id', 1);
                    $this->_preparePasswordForSave($customer, $customerData);
                }

                // Before save
                foreach ($customerData as $property => $value) {
                    $customer->setDataUsingMethod($property, $value);
                }
                $this->_prepareCustomerAddressesForSave($customer, $addressesData);
                $this->_eventManager->dispatch('adminhtml_customer_prepare_save', array(
                        'customer' => $customer,
                        'request' => $request
                    )
                );

                // Save customer
                $customer->save();

                // After save
                $this->_eventManager->dispatch('adminhtml_customer_save_after', array(
                        'customer' => $customer,
                        'request' => $request
                    )
                );
                $this->_sendWelcomeEmail($customer, $customerData);
                if ($isExistingCustomer) {
                    $this->_changePassword($customer, $customerData);
                }

                // Done Saving customer, finish save action
                $this->_objectManager->get('Magento\Core\Model\Registry')
                    ->register(RegistryConstants::CURRENT_CUSTOMER, $customer);
                $this->_objectManager->get('Magento\Core\Model\Registry')
                    ->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customer->getId());

                $this->messageManager->addSuccess(__('You saved the customer.'));

                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
                $customerId = $customer->getId();
            } catch (\Magento\Validator\ValidatorException $exception) {
                $this->_addSessionErrorMessages($exception->getMessages());
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (\Magento\Core\Exception $exception) {
                $messages = $exception->getMessages(\Magento\Message\MessageInterface::TYPE_ERROR);
                if (!count($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException($exception,
                    __('An error occurred while saving the customer.'));
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            }
        }

        if ($returnToEdit) {
            if ($customerId) {
                $this->_redirect('customer/*/edit', array('id' => $customerId, '_current' => true));
            } else {
                $this->_redirect('customer/*/new', array('_current' => true));
            }
        } else {
            $this->_redirect('customer/index');
        }
    }

    /**
     * Load customer by its ID
     *
     * @param int|string $customerId
     * @return \Magento\Customer\Model\Customer
     * @throws \Magento\Core\Exception
     */
    private function _loadCustomerById($customerId)
    {
        $customer = $this->_customerFactory->create();
        $customer->load($customerId);
        if (!$customer->getId()) {
            throw new \Magento\Core\Exception(__("The customer with the specified ID not found."));
        }

        return $customer;
    }

    /**
     * Save customer addresses.
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param array $addressesData
     * @throws \Magento\Core\Exception
     */
    private function _prepareCustomerAddressesForSave($customer, array $addressesData)
    {
        $hasChanges = $customer->hasDataChanges();
        $actualAddressesIds = array();
        foreach ($addressesData as $addressData) {
            $addressId = null;
            if (array_key_exists('entity_id', $addressData)) {
                $addressId = $addressData['entity_id'];
                unset($addressData['entity_id']);
            }

            if (null !== $addressId) {
                $address = $customer->getAddressItemById($addressId);
                if (!$address || !$address->getId()) {
                    throw new \Magento\Core\Exception(
                        __('The address with the specified ID not found.')
                    );
                }
            } else {
                $address = $this->_addressFactory->create();
                $address->setCustomerId($customer->getId());
                // Add customer address into addresses collection
                $customer->addAddress($address);
            }
            $address->addData($addressData);
            $hasChanges = $hasChanges || $address->hasDataChanges();

            // Set post_index for detect default billing and shipping addresses
            $address->setPostIndex($addressId);

            $actualAddressesIds[] = $address->getId();
        }

        /** @var \Magento\Customer\Model\Address $address */
        foreach ($customer->getAddressesCollection() as $address) {
            if (!in_array($address->getId(), $actualAddressesIds)) {
                $address->setData('_deleted', true);
                $hasChanges = true;
            }
        }
        $customer->setDataChanges($hasChanges);
    }

    /**
     * Send welcome email to customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param array $customerData
     */
    private function _sendWelcomeEmail($customer, array $customerData)
    {
        $isSendEmail = !empty($customerData['sendemail']);

        if ($customer->getWebsiteId()
            && ($isSendEmail || $this->_isAutogeneratePassword($customerData))
        ) {
            $isNewCustomer = !(bool)$customer->getOrigData($customer->getIdFieldName());
            $storeId = $customer->getSendemailStoreId();

            if ($isNewCustomer) {
                $newLinkToken = $this->_dataHelper->generateResetPasswordLinkToken();
                $customer->changeResetPasswordLinkToken($newLinkToken);
                $customer->sendNewAccountEmail('registered', '', $storeId);
            } elseif (!$customer->getConfirmation()) {
                // Confirm not confirmed customer
                $customer->sendNewAccountEmail('confirmed', '', $storeId);
            }
        }
    }

    /**
     * Check if password should be generated automatically
     *
     * @param array $customerData
     * @return bool
     */
    private function _isAutogeneratePassword(array $customerData)
    {
        return !empty($customerData['autogenerate_password']);
    }

    /**
     * Change customer password
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param array $customerData
     */
    private function _changePassword($customer, array $customerData)
    {
        if (!empty($customerData['password']) || $this->_isAutogeneratePassword($customerData)) {
            $newPassword = $this->_getCustomerPassword($customer, $customerData);
            $customer->changePassword($newPassword);
            $customer->sendPasswordReminderEmail();
        }
    }

    /**
     * Get customer password
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param array $customerData
     * @return string|null
     */
    private function _getCustomerPassword($customer, array $customerData)
    {
        $password = null;

        if ($this->_isAutogeneratePassword($customerData)) {
            $password = $customer->generatePassword();
        } elseif (isset($customerData['password'])) {
            $password = $customerData['password'];
        }

        return $password;
    }

    /**
     * Set customer password
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param array $customerData
     */
    private function _preparePasswordForSave($customer, array $customerData)
    {
        $password = $this->_getCustomerPassword($customer, $customerData);
        if (!is_null($password)) {
            // 'force_confirmed' should be set in admin area only
            $customer->setForceConfirmed(true);
            $customer->setPassword($password);
        }
    }

    /**
     * Reset password handler
     */
    public function resetPasswordAction()
    {
        $customerId = (int)$this->getRequest()->getParam('customer_id', 0);
        if (!$customerId) {
            return $this->_redirect('customer/index');
        }

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customer->load($customerId);
        if (!$customer->getId()) {
            return $this->_redirect('customer/index');
        }

        try {
            $newPasswordToken = $this->_objectManager->get('Magento\Customer\Helper\Data')
                ->generateResetPasswordLinkToken();
            $customer->changeResetPasswordLinkToken($newPasswordToken);
            $resetUrl = $this->_objectManager->create('Magento\UrlInterface')
                ->getUrl('customer/account/createPassword', array(
                        '_query' => array('id' => $customer->getId(), 'token' => $newPasswordToken),
                        '_store' => $customer->getStoreId()
                    )
                );
            $customer->setResetPasswordUrl($resetUrl);
            $customer->sendPasswordReminderEmail();
            $this->messageManager->addSuccess(__('Customer will receive an email with a link to reset password.'));
        } catch (\Magento\Core\Exception $exception) {
            $messages = $exception->getMessages(\Magento\Message\MessageInterface::TYPE_ERROR);
            if (!count($messages)) {
                $messages = $exception->getMessage();
            }
            $this->_addSessionErrorMessages($messages);
        } catch (\Exception $exception) {
            $this->messageManager->addException($exception,
                __('An error occurred while resetting customer password.'));
        }

        $this->_redirect('customer/*/edit', array('id' => $customerId, '_current' => true));
    }

    /**
     * Add errors messages to session.
     *
     * @param array|string $messages
     */
    protected function _addSessionErrorMessages($messages)
    {
        $messages = (array)$messages;
        $session = $this->_getSession();

        $callback = function ($error) use ($session) {
            if (!($error instanceof \Magento\Message\Error)) {
                $error = new \Magento\Message\Error($error);
            }
            $this->messageManager->addMessage($error);
        };
        array_walk_recursive($messages, $callback);
    }

    /**
     * Reformat customer account data to be compatible with customer service interface
     *
     * @return array
     */
    protected function _extractCustomerData()
    {
        $customerData = array();
        if ($this->getRequest()->getPost('account')) {
            $serviceAttributes = array(
                'new_password', 'default_billing', 'default_shipping', 'confirmation', 'sendemail');

            /** @var \Magento\Customer\Model\Customer $customerEntity */
            $customerEntity = $this->_objectManager->get('Magento\Customer\Model\CustomerFactory')->create();
            /** @var \Magento\Customer\Helper\Data $customerHelper */
            $customerHelper = $this->_objectManager->get('Magento\Customer\Helper\Data');
            $customerData = $customerHelper->extractCustomerData(
                $this->getRequest(), 'adminhtml_customer', $customerEntity, $serviceAttributes, 'account'
            );
        }

        if (!$this->getRequest()->getPost('customer_id')) {
            $customerData['new_password'] = 'auto';
        }
        $this->_processCustomerPassword($customerData);
        if ($this->_authorization->isAllowed(null)) {
            $customerData['is_subscribed'] = $this->getRequest()->getPost('subscription') !== null;
        }

        if (isset($customerData['disable_auto_group_change'])) {
            $customerData['disable_auto_group_change'] = empty($customerData['disable_auto_group_change']) ? '0' : '1';
        }

        return $customerData;
    }

    /**
     * Reformat customer addresses data to be compatible with customer service interface
     *
     * @return array
     */
    protected function _extractCustomerAddressData()
    {
        $addresses = $this->getRequest()->getPost('address');
        $customerData = $this->getRequest()->getPost('account');
        $result = array();
        if ($addresses) {
            if (isset($addresses['_template_'])) {
                unset($addresses['_template_']);
            }

            /** @var \Magento\Customer\Model\Address\Form $eavForm */
            $eavForm = $this->_objectManager->create('Magento\Customer\Model\Address\Form');
            /** @var \Magento\Customer\Model\Address $addressEntity */
            $addressEntity = $this->_objectManager->get('Magento\Customer\Model\AddressFactory')->create();

            $addressIdList = array_keys($addresses);
            /** @var \Magento\Customer\Helper\Data $customerHelper */
            $customerHelper = $this->_objectManager->get('Magento\Customer\Helper\Data');
            foreach ($addressIdList as $addressId) {
                $scope = sprintf('address/%s', $addressId);
                $addressData = $customerHelper->extractCustomerData(
                    $this->getRequest(), 'adminhtml_customer_address', $addressEntity, array(), $scope, $eavForm);
                if (is_numeric($addressId)) {
                    $addressData['entity_id'] = $addressId;
                }
                // Set default billing and shipping flags to address
                $addressData['is_default_billing'] = isset($customerData['default_billing'])
                    && $customerData['default_billing']
                    && $customerData['default_billing'] == $addressId;
                $addressData['is_default_shipping'] = isset($customerData['default_shipping'])
                    && $customerData['default_shipping']
                    && $customerData['default_shipping'] == $addressId;

                $result[] = $addressData;
            }
        }

        return $result;
    }

    /**
     * Generate password if auto generated password was requested
     *
     * @param array $customerData
     * @throws \Magento\Core\Exception
     */
    protected function _processCustomerPassword(&$customerData)
    {
        if (!empty($customerData['new_password'])) {
            if ($customerData['new_password'] == 'auto') {
                $customerData['autogenerate_password'] = true;
            } else {
                $customerData['password'] = $customerData['new_password'];
            }
        }
        unset($customerData['new_password']);
    }

    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'customers.csv';
        $content = $this->_view->getLayout()->createBlock('Magento\Customer\Block\Adminhtml\Grid')->getCsvFile();

        return $this->_fileFactory->create($fileName, $content, \Magento\App\Filesystem::VAR_DIR);
    }

    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName = 'customers.xml';
        $content = $this->_view->getLayout()->createBlock('Magento\Customer\Block\Adminhtml\Grid')->getExcelFile();
        return $this->_fileFactory->create($fileName, $content, \Magento\App\Filesystem::VAR_DIR);
    }

    /**
     * Customer orders grid
     */
    public function ordersAction()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Customer last orders grid for ajax
     */
    public function lastOrdersAction()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Customer newsletter grid
     */
    public function newsletterAction()
    {
        $this->_initCustomer();
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        $subscriber = $this->_objectManager->create('Magento\Newsletter\Model\Subscriber')
            ->loadByCustomer($customerId);

        $this->_coreRegistry->register('subscriber', $subscriber);
        $this->_view->loadLayout()->renderLayout();
    }

    public function wishlistAction()
    {
        $this->_initCustomer();
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        $itemId = (int)$this->getRequest()->getParam('delete');
        if ($customerId && $itemId) {
            try {
                $this->_objectManager->create('Magento\Wishlist\Model\Item')->load($itemId)
                    ->delete();
            } catch (\Exception $exception) {
                $this->_objectManager->get('Magento\Logger')->logException($exception);
            }
        }

        $this->_view->getLayout()->getUpdate()->addHandle(strtolower($this->_request->getFullActionName()));
        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
        $this->_view->renderLayout();
    }

    /**
     * Customer last view wishlist for ajax
     */
    public function viewWishlistAction()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * [Handle and then] get a cart grid contents
     *
     * @return string
     */
    public function cartAction()
    {
        $this->_initCustomer();
        $websiteId = $this->getRequest()->getParam('website_id');

        // delete an item from cart
        $deleteItemId = $this->getRequest()->getPost('delete');
        if ($deleteItemId) {
            $quote = $this->_objectManager->create('Magento\Sales\Model\Quote')
                ->setWebsite(
                    $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')->getWebsite($websiteId)
                )
                ->loadByCustomer($this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));
            $item = $quote->getItemById($deleteItemId);
            if ($item && $item->getId()) {
                $quote->removeItem($deleteItemId);
                $quote->collectTotals()->save();
            }
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('admin.customer.view.edit.cart')->setWebsiteId($websiteId);
        $this->_view->renderLayout();
    }

    /**
     * Get shopping cart to view only
     *
     */
    public function viewCartAction()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('admin.customer.view.cart')
            ->setWebsiteId((int)$this->getRequest()->getParam('website_id'));
        $this->_view->renderLayout();
    }

    /**
     * Get shopping carts from all websites for specified client
     *
     */
    public function cartsAction()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Get customer's product reviews list
     *
     */
    public function productReviewsAction()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('admin.customer.reviews')
            ->setCustomerId($this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID))
            ->setUseAjax(true);
        $this->_view->renderLayout();
    }

    /**
     * AJAX customer validation action
     */
    public function validateAction()
    {
        $response = new \Magento\Object();
        $response->setError(0);

        $customer = $this->_validateCustomer($response);
        if ($customer) {
            $this->_validateCustomerAddress($response, $customer);
        }

        if ($response->getError()) {
            $this->_view->getLayout()->initMessages();
            $response->setMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Customer validation
     *
     * @param \Magento\Object $response
     * @return \Magento\Customer\Model\Customer|null
     */
    protected function _validateCustomer($response)
    {
        $customer = null;
        $errors = null;

        try {
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
            $customerId = $this->getRequest()->getParam('id');
            if ($customerId) {
                $customer->load($customerId);
            }

            /* @var $customerForm \Magento\Customer\Model\Form */
            $customerForm = $this->_objectManager->get('Magento\Customer\Model\Form');
            $customerForm->setEntity($customer)
                ->setFormCode('adminhtml_customer')
                ->setIsAjaxRequest(true)
                ->ignoreInvisible(false);
            $data = $customerForm->extractData($this->getRequest(), 'account');
            $accountData = $this->getRequest()->getPost('account');
            $data['password'] = isset($accountData['password']) ? $accountData['password'] : '';
            if (!$customer->getId()) {
                $data['password'] = $customer->generatePassword();
            }
            $data['confirmation'] = $data['password'];

            if ($customer->getWebsiteId()) {
                unset($data['website_id']);
            }

            $customer->addData($data);
            $errors = $customer->validate();
        } catch (\Magento\Core\Exception $exception) {
            /* @var $error \Magento\Message\Error */
            foreach ($exception->getMessages(\Magento\Message\MessageInterface::TYPE_ERROR) as $error) {
                $errors[] = $error->getText();
            }
        }

        if ($errors !== true && !empty($errors)) {
            foreach ($errors as $error) {
                $this->messageManager->addError($error);
            }
            $response->setError(1);
        }

        return $customer;
    }

    /**
     * Customer address validation.
     *
     * @param \Magento\Object $response
     * @param \Magento\Customer\Model\Customer $customer
     */
    protected function _validateCustomerAddress($response, $customer)
    {
        $addressesData = $this->getRequest()->getParam('address');
        if (is_array($addressesData)) {
            /* @var $addressForm \Magento\Customer\Model\Form */
            $addressForm = $this->_objectManager->create('Magento\Customer\Model\Form');
            $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(false);
            foreach (array_keys($addressesData) as $index) {
                if ($index == '_template_') {
                    continue;
                }
                $address = $customer->getAddressItemById($index);
                if (!$address) {
                    $address   = $this->_objectManager->create('Magento\Customer\Model\Address');
                }

                $requestScope = sprintf('address/%s', $index);
                $formData = $addressForm->setEntity($address)
                    ->extractData($this->getRequest(), $requestScope);

                $errors = $addressForm->validateData($formData);
                if ($errors !== true) {
                    foreach ($errors as $error) {
                        $this->messageManager->addError($error);
                    }
                    $response->setError(1);
                }
            }
        }
    }

    /**
     * Customer mass subscribe action
     */
    public function massSubscribeAction()
    {
        $this->massCustomerChangeAction(function($customerId) {
            // Verify customer exists
            $this->_customerService->getCustomer($customerId);
            $this->_subscriberFactory->create()->updateSubscription($customerId, true);
        });
        $this->_redirect('customer/*/index');
    }

    /**
     * Customer mass unsubscribe action
     */
    public function massUnsubscribeAction()
    {
        $this->massCustomerChangeAction(function($customerId) {
            // Verify customer exists
            $this->_customerService->getCustomer($customerId);
            $this->_subscriberFactory->create()->updateSubscription($customerId, false);
        });
        $this->_redirect('customer/*/index');
    }

    /**
     * Customer mass delete action
     */
    public function massDeleteAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if (!is_array($customersIds)) {
            $this->messageManager->addError(__('Please select customer(s).'));
        } else {
            try {
                $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
                foreach ($customersIds as $customerId) {
                    $customer->reset()
                        ->load($customerId)
                        ->delete();
                }
                $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', count($customersIds)));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }

        $this->_redirect('customer/*/index');
    }

    /**
     * Customer mass assign group action
     */
    public function massAssignGroupAction()
    {
        $this->massCustomerChangeAction(function($customerId) {
            // Verify customer exists
            $customer = $this->_customerService->getCustomer($customerId);
            $this->_customerBuilder->populate($customer);
            $customer = $this->_customerBuilder
                ->setGroupId($this->getRequest()->getParam('group'))->create();
            $this->_customerService->saveCustomer($customer);
        });
        $this->_redirect('customer/*/index');
    }

    /**
     * Helper function that handles mass actions by taking in a callable for handling a single action.
     *
     * @param callable $singleAction that takes a customer ID as input
     */
    protected function massCustomerChangeAction(callable $singleAction)
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if (!is_array($customersIds)) {
            $this->messageManager->addError(__('Please select customer(s).'));
        } else {
            $customersUpdated = 0;
            foreach ($customersIds as $customerId) {
                try {
                    $singleAction($customerId);
                    $customersUpdated++;
                } catch (\Exception $exception) {
                    $this->messageManager->addError($exception->getMessage());
                }
            }
            if ($customersUpdated) {
                $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $customersUpdated));
            }
        }
    }

    /**
     * Customer view file action
     *
     * @throws NotFoundException
     */
    public function viewfileAction()
    {
        $file   = null;
        $plain  = false;
        if ($this->getRequest()->getParam('file')) {
            // download file
            $file   = $this->_objectManager->get('Magento\Core\Helper\Data')
                ->urlDecode($this->getRequest()->getParam('file'));
        } else if ($this->getRequest()->getParam('image')) {
            // show plain image
            $file   = $this->_objectManager->get('Magento\Core\Helper\Data')
                ->urlDecode($this->getRequest()->getParam('image'));
            $plain  = true;
        } else {
            throw new NotFoundException();
        }

        /** @var \Magento\App\Filesystem $filesystem */
        $filesystem = $this->_objectManager->get('Magento\App\Filesystem');
        $directory = $filesystem->getDirectoryRead(\Magento\App\Filesystem::MEDIA_DIR);
        $fileName = 'customer' . '/' . ltrim($file, '/');
        $path = $directory->getAbsolutePath($fileName);
        if (!$directory->isFile($fileName)
            && !$this->_objectManager->get('Magento\Core\Helper\File\Storage')
                ->processStorageFile($path)
        ) {
            throw new NotFoundException();
        }

        if ($plain) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'gif':
                    $contentType = 'image/gif';
                    break;
                case 'jpg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
                default:
                    $contentType = 'application/octet-stream';
                    break;
            }
            $stat = $directory->stat($path);
            $contentLength = $stat['size'];
            $contentModify = $stat['mtime'];

            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Last-Modified', date('r', $contentModify))
                ->clearBody();
            $this->getResponse()->sendHeaders();

            echo $directory->readFile($fileName);
        } else {
            $name = pathinfo($path, PATHINFO_BASENAME);
            $this->_fileFactory->create(
                $name,
                array(
                    'type'  => 'filename',
                    'value' => $fileName
                ),
                \Magento\App\Filesystem::MEDIA_DIR
            )->sendResponse();
        }

        exit();
    }

    /**
     * Customer access rights checking
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }
}
