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
        // Default address
        $defaultAddress = false;
        $addressModel = Mage::getModel('customer', 'address');
        
        if ($defaultAddressId = Mage_Customer_Front::getCustomerInfo('default_address_id')) {
            $defaultAddress = $addressModel->getRow($defaultAddressId);
        }
        
        // Load addresses
        $addressCoolection = Mage::getModel('customer', 'address_collection')
            ->addFilter('customer_id', (int) Mage_Customer_Front::getCustomerId(), 'and')
            ->addFilter('without_default', 'address_id!=' . (int) $defaultAddressId, 'string')
            ->load();
        
        $block = Mage::createBlock('tpl', 'customer.address')
            ->setViewName('Mage_Customer', 'address')
            ->assign('addresses', $addressCoolection->getItems())
            ->assign('defaultAddress', $defaultAddress);
        
        Mage::getBlock('content')->append($block);
    }
    
    /**
     * Address book form
     *
     */
    public function formAction()
    {
        $formData = new Varien_DataObject();
        $addressValidator = new Mage_Customer_Validate_Address(array());
        
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            
            // Validate address_id <=> customer_id
            if (!$addressValidator->hasCustomer($addressId, Mage_Customer_Front::getCustomerId())) {
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
                return;
            }
            $addressModel = Mage::getModel('customer', 'address');
            $formData = $addressModel->getRow($addressId);
        }
        
        // Save data
        if ($this->getRequest()->isPost()) {
            $addressValidator->setData($_POST);
            
            // Validate data
            if ($addressValidator->isValid()) {
                $addressModel = Mage::getModel('customer', 'address');
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
                    $customerModel = Mage::getModel('customer', 'customer');
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
        
        // Set street{$index} fields
        if ($street = $formData->getStreet()) {
            $street = explode("\n", $street);
            foreach ($street as $index => $value) {
                $formData->setData('street' . ($index+1), $value);
            }
        }
        
        $block = Mage::createBlock('tpl', 'customer.address.form')
            ->setViewName('Mage_Customer', 'form/address')
            ->assign('addressId', $addressId)
            ->assign('formData', $formData)
            ->assign('defaultAddressId', Mage_Customer_Front::getCustomerInfo('default_address_id'));
            
        Mage::getBlock('content')->append($block);
    }
    
    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        $addressValidator = new Mage_Customer_Validate_Address(array());
        
        if ($addressId) {
            
            // Validate address_id <=> customer_id
            if (!$addressValidator->hasCustomer($addressId, Mage_Customer_Front::getCustomerId())) {
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
                return;
            }
            $addressModel = Mage::getModel('customer', 'address');
            $addressModel->delete($addressId);
        }
        
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
    }
}// Class Mage_Customer_AccountController END
