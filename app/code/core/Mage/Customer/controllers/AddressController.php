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
        
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Default account page
     *
     */
    public function indexAction() 
    {
        $this->loadLayout();
        
        // Load addresses
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $addressCollection = Mage::getResourceModel('customer/address_collection');
        $addressCollection->loadByCustomerId($customerId);
        
        $block = $this->getLayout()->createBlock('core/template', 'customer.address')
            ->setTemplate('customer/address.phtml')
            ->assign('primaryAddresses', $addressCollection->getPrimaryAddresses())
            ->assign('alternativeAddresses', $addressCollection->getPrimaryAddresses(false))
            ->assign('messages', Mage::getSingleton('customer/session')->getMessages(true));
        
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    /**
     * Address book form
     *
     */
    public function formAction()
    {
        $this->loadLayout();
        
        $addressId = $this->getRequest()->getParam('address', false);
        $address = Mage::getModel('customer/address');
        $data = Mage::getSingleton('customer/session')->getAddressFormData(true);
        
        if ($addressId) {
            $address->load($addressId);
            
            // Validate address_id <=> customer_id
            if ($address->getCustomerId()!=Mage::getSingleton('customer/session')->getCustomerId()) {
                Mage::getSingleton('customer/session')
                    ->addMessage(Mage::getModel('customer/message')->error('CSTE020'));
                $this->getResponse()->setRedirect(Mage::getUrl('customer/address'));
                return;
            }
            
            $primary = $address->getPrimaryTypes();
        } else {
            $address->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
            $primary = array();
        }

        if(empty($data)){
            $data = $address;
        }

        $countries = Mage::getResourceModel('directory/country_collection');       
        // Form block
        $block = $this->getLayout()->createBlock('core/template', 'customer.address.form')
            ->setTemplate('customer/form/address.phtml')
            ->assign('action',      Mage::getUrl('customer/address/formPost'))
            ->assign('countries',   $countries->loadByStore())
            ->assign('regions',     $countries->getDefault($address->getCountryId())->getRegions())
            ->assign('address',     $address)
            ->assign('data',        $data)
            ->assign('messages',    Mage::getSingleton('customer/session')->getMessages(true))
            ->assign('primaryTypes',$address->getAvailableTypes());
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function formPostAction()
    {
        // Save data
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer/address')->setData($this->getRequest()->getPost());
            
            $url = Mage::getUrl('customer/address/form/address/'.$address->getAddressId());

            // Validate address_id <=> customer_id
            if ($address->getCustomerId()!==Mage::getSingleton('customer/session')->getCustomerId()) {
                Mage::getSingleton('customer/session')
                    ->addMessage(Mage::getModel('customer/message')->error('CSTE020'));
                $this->getResponse()->setRedirect($url);
                return;
            }
            
            try {
                $address->save();
                Mage::getSingleton('customer/session')
                    ->addMessage(Mage::getModel('customer/message')->success('CSTS004'));
                $this->getResponse()->setRedirect(Mage::getUrl('customer/address'));
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')
                    ->setAddressFormData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
            $this->getResponse()->setRedirect($url);
        }
    }
    
    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        
        if ($addressId) {
            $address = Mage::getModel('customer/address')->load($addressId);
            
            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
                Mage::getSingleton('customer/session')
                    ->addMessage(Mage::getModel('customer/message')->error('CSTE020'));
                $this->getResponse()->setRedirect(Mage::getUrl('customer/address'));
                return;
            }
            
            try {
                $address->delete();
                Mage::getSingleton('customer/session')
                    ->addMessage(Mage::getModel('customer/message')->success('CSTS005'));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('customer/session')
                    ->addMessages($e->getMessages());
            }
            catch (Exception $e){
                
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('customer/address'));
    }
}// Class Mage_Customer_AccountController END
