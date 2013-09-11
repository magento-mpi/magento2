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
namespace Magento\Customer\Controller;

class Address extends \Magento\Core\Controller\Front\Action
{
    /**
     * Retrieve customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->authenticate($this)) {
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
            $this->_initLayoutMessages('\Magento\Customer\Model\Session');
            $this->_initLayoutMessages('\Magento\Catalog\Model\Session');

            $block = $this->getLayout()->getBlock('address_book');
            if ($block) {
                $block->setRefererUrl($this->_getRefererUrl());
            }
            $this->renderLayout();
        } else {
            $this->getResponse()->setRedirect(\Mage::getUrl('*/*/new'));
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
        $this->_initLayoutMessages('\Magento\Customer\Model\Session');
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
            $this->_redirectError(\Mage::getUrl('*/*/edit'));
            return;
        }

        try {
            $address = $this->_extractAddress();
            $this->_validateAddress($address);
            $address->save();
            $this->_getSession()->addSuccess(__('The address has been saved.'));
            $this->_redirectSuccess(\Mage::getUrl('*/*/index', array('_secure'=>true)));
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
        } catch (\Magento\Validator\ValidatorException $e) {
            foreach ($e->getMessages() as $messages) {
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Cannot save address.'));
        }

        $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
        $this->_redirectError(\Mage::getUrl('*/*/edit', array('id' => $address->getId())));
    }

    /**
     * Do address validation using validate methods in models
     *
     * @param \Magento\Customer\Model\Address $address
     * @throws \Magento\Validator\ValidatorException
     */
    protected function _validateAddress($address)
    {
        $addressErrors = $address->validate();
        if (is_array($addressErrors) && count($addressErrors) > 0) {
            throw new \Magento\Validator\ValidatorException(array($addressErrors));
        }
    }

    /**
     * Extract address from request
     *
     * @return \Magento\Customer\Model\Address
     */
    protected function _extractAddress()
    {
        $customer = $this->_getSession()->getCustomer();
        /* @var \Magento\Customer\Model\Address $address */
        $address  = \Mage::getModel('\Magento\Customer\Model\Address');
        $addressId = $this->getRequest()->getParam('id');
        if ($addressId) {
            $existsAddress = $customer->getAddressById($addressId);
            if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                $address->load($existsAddress->getId());
            }
        }
        /* @var \Magento\Customer\Model\Form $addressForm */
        $addressForm = \Mage::getModel('\Magento\Customer\Model\Address\Form');
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
            $address = \Mage::getModel('\Magento\Customer\Model\Address')->load($addressId);

            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $this->_getSession()->addError(__('The address does not belong to this customer.'));
                $this->getResponse()->setRedirect(\Mage::getUrl('*/*/index'));
                return;
            }

            try {
                $address->delete();
                $this->_getSession()->addSuccess(__('The address has been deleted.'));
            } catch (\Exception $e){
                $this->_getSession()->addException($e, __('An error occurred while deleting the address.'));
            }
        }
        $this->getResponse()->setRedirect(\Mage::getUrl('*/*/index'));
    }
}
