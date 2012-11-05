<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer admin controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_CustomerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * Event manager
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Registry model
     *
     * @var Mage_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * ACL
     *
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_acl;

    /**
     * @var Mage_Customer_Service_Customer
     */
    protected $_customerService;

    /**
     * @var Mage_Customer_Helper_Data
     */
    protected $_customerHelper;

    /**
     * @var Magento_Validator
     */
    protected $_validator;

    /**
     * Constructor
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs
     */
    public function __construct(Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response, array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $invokeArgs);

        if (isset($invokeArgs['objectFactory'])) {
            $this->_objectFactory = $invokeArgs['objectFactory'];
        } else {
            $this->_objectFactory = Mage::getConfig();
        }

        if (isset($invokeArgs['registry'])) {
            $this->_registryManager = $invokeArgs['registry'];
        } else {
            $this->_registryManager = Mage::getSingleton('Mage_Core_Model_Registry');
        }

        if (isset($invokeArgs['acl'])) {
            $this->_acl = $invokeArgs['acl'];
        } else {
            $this->_acl = Mage::getSingleton('Mage_Core_Model_Authorization');
        }

        if (isset($invokeArgs['eventManager'])) {
            $this->_eventManager = $invokeArgs['eventManager'];
        } else {
            $this->_eventManager = Mage::getSingleton('Mage_Core_Model_Event_Manager');
        }

        if (isset($invokeArgs['customerService'])) {
            $this->_customerService = $invokeArgs['customerService'];
        } else {
            $this->_customerService = $this->_objectFactory->getModelInstance('Mage_Customer_Service_Customer');
        }

        if (isset($invokeArgs['customerHelper'])) {
            $this->_customerHelper = $invokeArgs['customerHelper'];
        } else {
            $this->_customerHelper = Mage::helper('Mage_Customer_Helper_Data');
        }
    }

    /**
     * Customer initialization
     *
     * @param string $idFieldName
     * @return Mage_Adminhtml_CustomerController
     */
    protected function _initCustomer($idFieldName = 'id')
    {
        // Default title
        $this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));

        $customerId = (int)$this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    /**
     * Customers list action
     */
    public function indexAction()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('Mage_Customer::customer_manage');

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Customer', 'customer')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Customers'),
            Mage::helper('Mage_Adminhtml_Helper_Data')->__('Customers'));
        $this->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Manage Customers'),
            Mage::helper('Mage_Adminhtml_Helper_Data')->__('Manage Customers'));

        $this->renderLayout();
    }

    /**
     * Customer grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer edit action
     */
    public function editAction()
    {
        $this->_initCustomer();
        $this->loadLayout();

        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::registry('current_customer');

        // set entered data if was error when we do save
        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getCustomerData(true);

        // restore data from SESSION
        if ($data) {
            $request = clone $this->getRequest();
            $request->setParams($data);

            if (isset($data['account'])) {
                /* @var $customerForm Mage_Customer_Model_Form */
                $customerForm = Mage::getModel('Mage_Customer_Model_Form');
                $customerForm->setEntity($customer)
                    ->setFormCode('adminhtml_customer')
                    ->setIsAjaxRequest(true);
                $formData = $customerForm->extractData($request, 'account');
                $customerForm->restoreData($formData);
            }

            if (isset($data['address']) && is_array($data['address'])) {
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('Mage_Customer_Model_Form');
                $addressForm->setFormCode('adminhtml_customer_address');

                foreach (array_keys($data['address']) as $addressId) {
                    if ($addressId == '_template_') {
                        continue;
                    }

                    $address = $customer->getAddressItemById($addressId);
                    if (!$address) {
                        $address = Mage::getModel('Mage_Customer_Model_Address');
                        $customer->addAddress($address);
                    }

                    $formData = $addressForm->setEntity($address)
                        ->extractData($request);
                    $addressForm->restoreData($formData);
                }
            }
        }

        $this->_title($customer->getId() ? $customer->getName() : $this->__('New Customer'));

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('Mage_Customer::customer');

        $this->renderLayout();
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
        $customer = Mage::registry('current_customer');
        if ($customer->getId()) {
            try {
                $customer->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('The customer has been deleted.'));
            }
            catch (Exception $exception){
                $this->_getSession()->addError($exception->getMessage());
            }
        }
        $this->_redirect('*/customer');
    }

    /**
     * Save customer action
     */
    public function saveAction()
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = null;
        $returnToEdit = false;
        if ($originalRequestData = $this->getRequest()->getPost()) {
            try {
                // optional fields might be set in request for future processing by observers in other modules
                $customerData = $originalRequestData;
                $customerData['account'] = $this->_extractCustomerData();
                $customerData['addresses'] = $this->_extractCustomerAddressData();

                $customerId = (int)$this->getRequest()->getPost('customer_id');
                if ($customerId) {
                    $customer = $this->_customerService->update($customerId, $customerData['account'], true);
                } else {
                    $customer = $this->_customerService->create($customerData['account']);
                }

                $this->_saveCustomerAddresses($customer, $customerData);

                $this->_registryManager->register('current_customer', $customer);
                $this->_getSession()->addSuccess($this->_getHelper()->__('The customer has been saved.'));

                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            } catch (Magento_Validator_Exception $exception) {
                $this->_addSessionErrorMessages($exception->getMessages());
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (Mage_Core_Exception $exception) {
                $messages = $exception->getMessages(Mage_Core_Model_Message::ERROR);
                if (!count($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (Exception $exception) {
                $this->_getSession()->addException($exception,
                    $this->_getHelper()->__('An error occurred while saving the customer.'));
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            }
        }

        if ($returnToEdit) {
            $returnParams = array('_current' => true);
            if ($customer) {
                $returnParams['id'] = $customer->getId();
            }
            $this->_redirect('*/*/edit', $returnParams);
        } else {
            $this->_redirect('*/customer');
        }
    }

    /**
     * Save customer addresses.
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerData
     * @throws Mage_Core_Exception
     */
    protected function _saveCustomerAddresses($customer, array $customerData)
    {
        $actualAddressesIds = array();
        foreach ($customerData['addresses'] as $addressId => $addressData) {
            /** @var Mage_Customer_Model_Address $address */
            $address = Mage::getModel('Mage_Customer_Model_Address');

            if (is_numeric($addressId)) {
                $address->load($addressId);
                if (!$address->getId()) {
                    throw new Mage_Core_Exception(
                        $this->_getHelper()->__('The address with the specified ID not found.'));
                }
            } else {
                $address->setCustomerId($customer->getId());
            }
            $address->addData($addressData);

            // Set default billing and shipping flags to address
            $isDefaultBilling = isset($customerData['account']['default_billing'])
                && $customerData['account']['default_billing'] == $addressId;
            $address->setIsDefaultBilling($isDefaultBilling);
            $isDefaultShipping = isset($customerData['account']['default_shipping'])
                && $customerData['account']['default_shipping'] == $addressId;
            $address->setIsDefaultShipping($isDefaultShipping);

            // Set post_index for detect default billing and shipping addresses
            $address->setPostIndex($addressId);

            $address->save();

            $actualAddressesIds[] = $address->getId();
        }

        $this->_deleteCustomerAddresses($customer, $actualAddressesIds);
    }

    /**
     * Delete customer addresses.
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $actualAddressesIds
     */
    protected function _deleteCustomerAddresses($customer, array $actualAddressesIds)
    {
        $hasDeletedAddresses = false;
        /** @var Mage_Customer_Model_Address $address */
        foreach ($customer->getAddressesCollection() as $address) {
            if ($address->getId() && !in_array($address->getId(), $actualAddressesIds)) {
                $address->setData('_deleted', true);
                $hasDeletedAddresses = true;
            }
        }
        if ($hasDeletedAddresses) {
            // Deleting of addresses triggered in Mage_Customer_Model_Resource_Customer::_beforeSave
            $customer->setDataChanges(true);
            $customer->save();
        }
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
            if (!($error instanceof Mage_Core_Model_Message_Error)) {
                $error = new Mage_Core_Model_Message_Error($error);
            }
            $session->addMessage($error);
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
            $serviceAttributes = array('password', 'new_password', 'default_billing', 'default_shipping');
            $customerEntity = Mage::getModel('Mage_Customer_Model_Customer');
            $customerData = $this->_customerHelper
                ->extractCustomerData($this->getRequest(), 'adminhtml_customer', $customerEntity, $serviceAttributes,
                    'account');
        }

        $this->_processCustomerPassword($customerData);
        if ($this->_acl->isAllowed(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL)) {
            $subscription = $this->getRequest()->getPost('subscription', false);
            if (!empty($subscription)) {
                $customerData['is_subscribed'] = true;
            }
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
        if ($addresses) {
            if (isset($addresses['_template_'])) {
                unset($addresses['_template_']);
            }

            /** @var Mage_Customer_Model_Address_Form $eavForm */
            $eavForm = Mage::getModel('Mage_Customer_Model_Address_Form');
            $addressEntity = Mage::getModel('Mage_Customer_Model_Address');

            $addressIdList = array_keys($addresses);
            foreach ($addressIdList as $addressId) {
                $scope = sprintf('address/%s', $addressId);
                $addresses[$addressId] = $this->_customerHelper
                    ->extractCustomerData($this->getRequest(), 'adminhtml_customer_address', $addressEntity, array(),
                        $scope, $eavForm);
            }
        }

        return $addresses;
    }

    /**
     * Generate password if auto generated password was requested
     *
     * @param array $customerData
     * @throws Mage_Core_Exception
     */
    protected function _processCustomerPassword(&$customerData)
    {
        if (isset($customerData['new_password']) && $customerData['new_password'] !== false) {
            $customerData['password'] = $customerData['new_password'];
            unset($customerData['new_password']);
        }
        if (isset($customerData['password']) && ($customerData['password'] == 'auto')) {
            unset($customerData['password']);
            $customerData['autogenerate_password'] = true;
        }
        $customerData['confirmation'] = $customerData['password'];

        if (empty($customerData['autogenerate_password'])) {
            /** @var Magento_Validator_Config $validatorFactory */
            $validatorFactory = Mage::getConfig()->getValidatorConfig();
            $passwordValidator = $validatorFactory->createValidator('customer', 'adminhtml_password_check');
            if (!$passwordValidator->isValid($customerData)) {
                $exception = new Mage_Core_Exception();
                /* @var $messageFactory Mage_Core_Model_Message */
                $messageFactory = Mage::getSingleton('Mage_Core_Model_Message');
                foreach ($passwordValidator->getMessages() as $error) {
                    foreach ($error as $errorMessage) {
                        $exception->addMessage($messageFactory->error($errorMessage));
                    }
                }

                throw $exception;
            }
        }
    }

    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'customers.csv';
        $content = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Customer_Grid')->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'customers.xml';
        $content    = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Customer_Grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Customer orders grid
     *
     */
    public function ordersAction() {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer last orders grid for ajax
     *
     */
    public function lastOrdersAction() {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer newsletter grid
     *
     */
    public function newsletterAction()
    {
        $this->_initCustomer();
        $subscriber = Mage::getModel('Mage_Newsletter_Model_Subscriber')
            ->loadByCustomer(Mage::registry('current_customer'));

        Mage::register('subscriber', $subscriber);
        $this->loadLayout()
            ->renderLayout();
    }

    public function wishlistAction()
    {
        $this->_initCustomer();
        $customer = Mage::registry('current_customer');
        if ($customer->getId()) {
            if($itemId = (int) $this->getRequest()->getParam('delete')) {
                try {
                    Mage::getModel('Mage_Wishlist_Model_Item')->load($itemId)
                        ->delete();
                }
                catch (Exception $exception) {
                    Mage::logException($exception);
                }
            }
        }

        $this->getLayout()->getUpdate()
            ->addHandle(strtolower($this->getFullActionName()));
        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();

        $this->renderLayout();
    }

    /**
     * Customer last view wishlist for ajax
     *
     */
    public function viewWishlistAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
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
            $quote = Mage::getModel('Mage_Sales_Model_Quote')
                ->setWebsite(Mage::app()->getWebsite($websiteId))
                ->loadByCustomer(Mage::registry('current_customer'));
            $item = $quote->getItemById($deleteItemId);
            if ($item && $item->getId()) {
                $quote->removeItem($deleteItemId);
                $quote->collectTotals()->save();
            }
        }

        $this->loadLayout();
        $this->getLayout()->getBlock('admin.customer.view.edit.cart')->setWebsiteId($websiteId);
        $this->renderLayout();
    }

    /**
     * Get shopping cart to view only
     *
     */
    public function viewCartAction()
    {
        $this->_initCustomer();
        $this->loadLayout()
            ->getLayout()
            ->getBlock('admin.customer.view.cart')
            ->setWebsiteId((int)$this->getRequest()->getParam('website_id'));
        $this->renderLayout();
    }

    /**
     * Get shopping carts from all websites for specified client
     *
     */
    public function cartsAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get customer's product reviews list
     *
     */
    public function productReviewsAction()
    {
        $this->_initCustomer();
        $this->loadLayout()
            ->getLayout()
            ->getBlock('admin.customer.reviews')
            ->setCustomerId(Mage::registry('current_customer')->getId())
            ->setUseAjax(true);
        $this->renderLayout();
    }

    /**
     * AJAX customer validation action
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(0);

        $customer = $this->_validateCustomer($response);
        if ($customer) {
            $this->_validateCustomerAddress($response, $customer);
        }

        if ($response->getError()) {
            $this->_initLayoutMessages('Mage_Adminhtml_Model_Session');
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Customer validation
     *
     * @param Varien_Object $response
     * @return Mage_Customer_Model_Customer|null
     */
    protected function _validateCustomer($response)
    {
        $customer = null;
        $errors = null;

        try {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = $this->_objectFactory->getModelInstance('Mage_Customer_Model_Customer');
            $customerId = $this->getRequest()->getParam('id');
            if ($customerId) {
                $customer->load($customerId);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = $this->_objectFactory->getModelInstance('Mage_Customer_Model_Form');
            $customerForm->setEntity($customer)
                ->setFormCode('adminhtml_customer')
                ->setIsAjaxRequest(true)
                ->ignoreInvisible(false);
            $data = $customerForm->extractData($this->getRequest(), 'account');
            $accountData = $this->getRequest()->getPost('account');
            $this->_processCustomerPassword($accountData);
            if (isset($accountData['autogenerate_password'])) {
                $data['password'] = $customer->generatePassword();
            } else {
                $data['password'] = $accountData['password'];
            }
            $data['confirmation'] = $data['password'];

            if ($customer->getWebsiteId()) {
                unset($data['website_id']);
            }

            $customer->addData($data);
            $errors = $customer->validate();
        } catch (Mage_Core_Exception $exception) {
            /* @var $error Mage_Core_Model_Message_Error */
            foreach ($exception->getMessages(Mage_Core_Model_Message::ERROR) as $error) {
                $errors[] = $error->getCode();
            }
        }

        if ($errors !== true && !empty($errors)) {
            foreach ($errors as $error) {
                $this->_getSession()->addError($error);
            }
            $response->setError(1);
        }

        return $customer;
    }

    /**
     * Customer validation
     * @param Varien_Object $response
     * @param Mage_Customer_Model_Customer $customer
     */
    protected function _validateCustomerAddress($response, $customer)
    {
        $addressesData = $this->getRequest()->getParam('address');
        if (is_array($addressesData)) {
            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('Mage_Customer_Model_Form');
            $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(false);
            foreach (array_keys($addressesData) as $index) {
                if ($index == '_template_') {
                    continue;
                }
                $address = $customer->getAddressItemById($index);
                if (!$address) {
                    $address   = Mage::getModel('Mage_Customer_Model_Address');
                }

                $requestScope = sprintf('address/%s', $index);
                $formData = $addressForm->setEntity($address)
                    ->extractData($this->getRequest(), $requestScope);

                $errors = $addressForm->validateData($formData);
                if ($errors !== true) {
                    foreach ($errors as $error) {
                        $this->_getSession()->addError($error);
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
        $customersIds = $this->getRequest()->getParam('customer');
        if(!is_array($customersIds)) {
             Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Please select customer(s).'));
        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($customerId);
                    $customer->setIsSubscribed(true);
                    $customer->save();
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $exception) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($exception->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Customer mass unsubscribe action
     */
    public function massUnsubscribeAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if(!is_array($customersIds)) {
             Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Please select customer(s).'));
        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($customerId);
                    $customer->setIsSubscribed(false);
                    $customer->save();
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $exception) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($exception->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Customer mass delete action
     */
    public function massDeleteAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if(!is_array($customersIds)) {
             Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Please select customer(s).'));
        } else {
            try {
                $customer = Mage::getModel('Mage_Customer_Model_Customer');
                foreach ($customersIds as $customerId) {
                    $customer->reset()
                        ->load($customerId)
                        ->delete();
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('Total of %d record(s) were deleted.', count($customersIds))
                );
            } catch (Exception $exception) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($exception->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Customer mass assign group action
     */
    public function massAssignGroupAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if(!is_array($customersIds)) {
             Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Please select customer(s).'));
        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($customerId);
                    $customer->setGroupId($this->getRequest()->getParam('group'));
                    $customer->save();
                }
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $exception) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($exception->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Customer view file action
     */
    public function viewfileAction()
    {
        $file   = null;
        $plain  = false;
        if ($this->getRequest()->getParam('file')) {
            // download file
            $file   = Mage::helper('Mage_Core_Helper_Data')->urlDecode($this->getRequest()->getParam('file'));
        } else if ($this->getRequest()->getParam('image')) {
            // show plain image
            $file   = Mage::helper('Mage_Core_Helper_Data')->urlDecode($this->getRequest()->getParam('image'));
            $plain  = true;
        } else {
            return $this->norouteAction();
        }

        $path = Mage::getBaseDir('media') . DS . 'customer';

        $ioFile = new Varien_Io_File();
        $ioFile->open(array('path' => $path));
        $fileName   = $ioFile->getCleanPath($path . $file);
        $path       = $ioFile->getCleanPath($path);

        if ((!$ioFile->fileExists($fileName) || strpos($fileName, $path) !== 0)
            && !Mage::helper('Mage_Core_Helper_File_Storage')->processStorageFile(str_replace('/', DS, $fileName))
        ) {
            return $this->norouteAction();
        }

        if ($plain) {
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
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

            $ioFile->streamOpen($fileName, 'r');
            $contentLength = $ioFile->streamStat('size');
            $contentModify = $ioFile->streamStat('mtime');

            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Last-Modified', date('r', $contentModify))
                ->clearBody();
            $this->getResponse()->sendHeaders();

            while (false !== ($buffer = $ioFile->streamRead())) {
                echo $buffer;
            }
        } else {
            $name = pathinfo($fileName, PATHINFO_BASENAME);
            $this->_prepareDownloadResponse($name, array(
                'type'  => 'filename',
                'value' => $fileName
            ));
        }

        exit();
    }

    /**
     * Customer access rights checking
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Customer::manage');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data['account'] = $this->_filterDates($data['account'], array('dob'));
        return $data;
    }
}
