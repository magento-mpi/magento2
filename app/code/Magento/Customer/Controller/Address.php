<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer address controller
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Controller_Address extends Magento_Core_Controller_Front_Action
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Customer_Model_AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var Magento_Customer_Model_Address_FormFactory
     */
    protected $_addressFormFactory;

    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Customer_Model_AddressFactory $addressFactory,
        Magento_Customer_Model_Address_FormFactory $addressFormFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->_addressFactory = $addressFactory;
        $this->_addressFormFactory = $addressFormFactory;
        parent::__construct($context);
    }

    /**
     * Retrieve customer session object
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_getSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Customer addresses list
     */
    public function indexAction()
    {
        if (count($this->_getSession()->getCustomer()->getAddresses())) {
            $this->loadLayout();
            $this->_initLayoutMessages('Magento_Customer_Model_Session');
            $this->_initLayoutMessages('Magento_Catalog_Model_Session');

            $block = $this->getLayout()->getBlock('address_book');
            if ($block) {
                $block->setRefererUrl($this->_getRefererUrl());
            }
            $this->renderLayout();
        } else {
            $this->getResponse()->setRedirect($this->_buildUrl('*/*/new'));
        }
    }

    public function editAction()
    {
        $this->_forward('form');
    }

    public function newAction()
    {
        $this->_forward('form');
    }

    /**
     * Address book form
     */
    public function formAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('customer/address');
        }
        $this->renderLayout();
    }

    /**
     * Process address form save
     */
    public function formPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }

        if (!$this->getRequest()->isPost()) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
            $this->_redirectError($this->_buildUrl('*/*/edit'));
            return;
        }

        try {
            $address = $this->_extractAddress();
            $this->_validateAddress($address);
            $address->save();
            $this->_getSession()->addSuccess(__('The address has been saved.'));
            $this->_redirectSuccess($this->_buildUrl('*/*/index', array('_secure'=>true)));
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
        } catch (Magento_Validator_Exception $e) {
            foreach ($e->getMessages() as $messages) {
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, __('Cannot save address.'));
        }

        $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
        $this->_redirectError($this->_buildUrl('*/*/edit', array('id' => $address->getId())));
    }

    /**
     * Do address validation using validate methods in models
     *
     * @param Magento_Customer_Model_Address $address
     * @throws Magento_Validator_Exception
     */
    protected function _validateAddress($address)
    {
        $addressErrors = $address->validate();
        if (is_array($addressErrors) && count($addressErrors) > 0) {
            throw new Magento_Validator_Exception(array($addressErrors));
        }
    }

    /**
     * Extract address from request
     *
     * @return Magento_Customer_Model_Address
     */
    protected function _extractAddress()
    {
        $customer = $this->_getSession()->getCustomer();
        /* @var Magento_Customer_Model_Address $address */
        $address  = $this->_createAddress();
        $addressId = $this->getRequest()->getParam('id');
        if ($addressId) {
            $existsAddress = $customer->getAddressById($addressId);
            if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                $address->load($existsAddress->getId());
            }
        }
        /* @var Magento_Customer_Model_Form $addressForm */
        $addressForm = $this->_createAddressForm();
        $addressForm->setFormCode('customer_address_edit')
            ->setEntity($address);
        $addressData = $addressForm->extractData($this->getRequest());
        $addressForm->compactData($addressData);
        $address->setCustomerId($customer->getId())
            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
        return $address;
    }

    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId) {
            $address = $this->_createAddress();
            $address->load($addressId);

            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $this->_getSession()->addError(__('The address does not belong to this customer.'));
                $this->getResponse()->setRedirect($this->_buildUrl('*/*/index'));
                return;
            }

            try {
                $address->delete();
                $this->_getSession()->addSuccess(__('The address has been deleted.'));
            } catch (Exception $e){
                $this->_getSession()->addException($e, __('An error occurred while deleting the address.'));
            }
        }
        $this->getResponse()->setRedirect($this->_buildUrl('*/*/index'));
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _buildUrl($route = '', $params = array())
    {
        /** @var Magento_Core_Model_Url $urlBuilder */
        $urlBuilder = $this->_objectManager->create('Magento_Core_Model_Url');
        return $urlBuilder->getUrl($route, $params);
    }

    /**
     * @return Magento_Customer_Model_Address
     */
    protected function _createAddress()
    {
        return $this->_addressFactory->create();
    }

    /**
     * @return Magento_Customer_Model_Address_Form
     */
    protected function _createAddressForm()
    {
        return $this->_addressFormFactory->create();
    }
}
