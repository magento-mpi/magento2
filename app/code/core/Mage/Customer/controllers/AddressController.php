<?php
/**
 * Customer address controller
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_AddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Default account page
     *
     */
    public function indexAction() 
    {
        if (!Mage_Customer_Front::authenticate($this)) {
            return;
        }

        // Default address
        $defaultAddress = false;
        
        if ($defaultAddressId = Mage_Customer_Front::getCustomerInfo('default_address_id')) {
            $defaultAddress = new Mage_Customer_Address($defaultAddressId);
        }
        
        // Load addresses
        $addressCoolection = new Mage_Customer_Address_Collection();
        $addressCoolection->filterByCustomerId(Mage_Customer_Front::getCustomerId())
            ->filterByCondition('address_id!=' . (int) $defaultAddressId)
            ->load();
        
        $block = Mage::createBlock('tpl', 'customer.address')
            ->setViewName('Mage_Customer', 'address.phtml')
            ->assign('addresses', $addressCoolection->getAll())
            ->assign('defaultAddress', $defaultAddress);
        
        Mage::getBlock('content')->append($block);
    }
    
    /**
     * Address book form
     *
     */
    public function formAction()
    {
        if (!Mage_Customer_Front::authenticate($this)) {
            return;
        }
        
        // TODO: restore form data and messages from session
        
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            
            $address = new Mage_Customer_Address($addressId);
            
            // Validate address_id <=> customer_id
            if (!$address->hasCustomer(Mage_Customer_Front::getCustomerId())) {
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
                return;
            }
        }
        else {
            $address = new Mage_Customer_Address($addressId);
        }
        
        
        $block = Mage::createBlock('tpl', 'customer.address.form')
            ->setViewName('Mage_Customer', 'form/address.phtml')
            ->assign('addressId', $addressId)
            ->assign('formData', $address)
            ->assign('defaultAddressId', Mage_Customer_Front::getCustomerInfo('default_address_id'));
            
        Mage::getBlock('content')->append($block);
    }
    
    public function formPostAction()
    {
        // Save data
        if ($this->getRequest()->isPost()) {
            $addressValidator->setData($_POST);
            
            // Validate data
            if ($addressValidator->isValid()) {
                $addressModel = Mage::getResourceModel('customer', 'address');
                if ($addressId) {
                    $saveRes = $addressModel->update($addressValidator->getData(), $addressId);
                }
                else {
                    $saveData = $addressValidator->getData();
                    $saveData['customer_id'] = Mage_Customer_Front::getCustomerId();
                    $addressId = $addressModel->insert($saveData);
                }
                
                // Set default addres for customer
                if (!empty($_POST['set_as_default'])) {
                    $customerModel = Mage::getResourceModel('customer', 'customer');
                    $customerModel->setDefaultAddress(Mage_Customer_Front::getCustomerId(), $addressId);
                    Mage_Customer_Front::setCustomerInfo('default_address_id', $addressId);
                }
                
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
            }
            else {
                // validation error message
                $formData = $addressValidator->getDataObject();
            }
        }
    }
    
    public function deleteAction()
    {
        if (!Mage_Customer_Front::authenticate($this)) {
            return;
        }

        $addressId = $this->getRequest()->getParam('address', false);
        $addressValidator = new Mage_Customer_Validate_Address(array());
        
        if ($addressId) {
            
            // Validate address_id <=> customer_id
            if (!$addressValidator->hasCustomer($addressId, Mage_Customer_Front::getCustomerId())) {
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
                return;
            }
            $addressModel = Mage::getResourceModel('customer', 'address');
            $addressModel->delete($addressId);
        }
        
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
    }
}// Class Mage_Customer_AccountController END
