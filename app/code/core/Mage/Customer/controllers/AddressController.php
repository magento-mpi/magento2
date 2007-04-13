<?php
/**
 * Customer address controller
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_AddressController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        
        if (!Mage::getSingleton('customer_model', 'session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Default account page
     *
     */
    public function indexAction() 
    {
        // Load addresses
        $customerId = Mage::getSingleton('customer_model', 'session')->getCustomerId();
        $addressCollection = Mage::getModel('customer', 'address_collection');
        $addressCollection->loadByCustomerId($customerId);
        
        $block = Mage::createBlock('tpl', 'customer.address')
            ->setViewName('Mage_Customer', 'address.phtml')
            ->assign('primaryAddresses', $addressCollection->getPrimaryAddresses())
            ->assign('alternativeAddresses', $addressCollection->getPrimaryAddresses(false))
            ->assign('messages', Mage::getSingleton('customer_model', 'session')->getMessages(true));
        
        Mage::getBlock('content')->append($block);
    }
    
    /**
     * Address book form
     *
     */
    public function formAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        $address = Mage::getModel('customer', 'address');
        $data = Mage::getSingleton('customer_model', 'session')->getData(true);
        
        if ($addressId) {
            $address->load($addressId);
            
            // Validate address_id <=> customer_id
            if ($address->getCustomerId()!=Mage::getSingleton('customer_model', 'session')->getCustomerId()) {
                Mage::getSingleton('customer_model', 'session')
                    ->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE020'));
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
                return;
            }
            
            $primary = $address->getPrimaryTypes();
        } else {
            $address->setCustomerId(Mage::getSingleton('customer_model', 'session')->getCustomerId());
            $primary = array();
        }

        if($data->isEmpty()){
            $data = $address;
        }

        $countries = Mage::getModel('directory', 'country_collection');       
        // Form block
        $block = Mage::createBlock('tpl', 'customer.address.form')
            ->setViewName('Mage_Customer', 'form/address.phtml')
            ->assign('action',      Mage::getBaseUrl('', 'Mage_Customer') . '/address/formPost/')
            ->assign('countries',   $countries->loadByCurrentDomain())
            ->assign('regions',     $countries->getDefault($address->getCountryId())->getRegions())
            ->assign('address',     $address)
            ->assign('data',        $data)
            ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true))
            ->assign('primaryTypes',$address->getAvailableTypes());
            
        Mage::getBlock('content')->append($block);
    }
    
    public function formPostAction()
    {
        // Save data
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer', 'address')->setData($this->getRequest()->getPost());
            
            $url = Mage::getBaseUrl('', 'Mage_Customer').'/address/form/';
            if ($address->getAddressId()) {
                $url.= 'address/' . $address->getAddressId() . '/';
            }

            // Validate address_id <=> customer_id
            if ($address->getCustomerId()!==Mage::getSingleton('customer_model', 'session')->getCustomerId()) {
                Mage::getSingleton('customer_model', 'session')
                    ->addMessage(Mage::getModel('customer_model', 'message')->error('CSTE020'));
                $this->_redirect($url);
                return;
            }
            
            try {
                $address->save();
                Mage::getSingleton('customer_model', 'session')
                    ->addMessage(Mage::getModel('customer_model', 'message')->success('CSTS004'));
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer_model', 'session')
                    ->setData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
            
            $this->_redirect($url);
        }
    }
    
    public function deleteAction()
    {
        if (!Mage::getSingleton('customer_model', 'session')->authenticate($this)) {
            return;
        }

        $addressId = $this->getRequest()->getParam('address', false);
        
        if ($addressId) {
            $address = Mage::getModel('customer', 'address')->load($addressId);
            
            // Validate address_id <=> customer_id
            if (!$addressValidator->hasCustomer($addressId, Mage::getSingleton('customer_model', 'session')->getCustomerId())) {
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
                return;
            }
            $addressModel = Mage::getModel('customer', 'address');
            $addressModel->delete($addressId);
        }
        
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
    }
}// Class Mage_Customer_AccountController END
