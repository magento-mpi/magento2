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
     * Customer addresses list
     */
    public function indexAction() 
    {   
        if (Mage::getSingleton('customer/session')->getCustomer()->getLoadedAddressCollection()->getSize())
        {
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
                 
            $this->getLayout()->getBlock('content')->append(
                $this->getLayout()->createBlock('customer/address_book')
            );
            $this->renderLayout(); 
        } else $this->getResponse()->setRedirect(Mage::getUrl('*/*/new'));
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
        $this->_initLayoutMessages('customer/session');
        
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('customer/address_edit')
        );
        
        $this->renderLayout();
    }
    
    public function formPostAction()
    {
        // Save data
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer/address')
                ->setData($this->getRequest()->getPost())
                ->setId($this->getRequest()->getParam('id'))
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
            
            try {
                $address->save();
                Mage::getSingleton('customer/session')
                    ->addSuccess('The address has been successfully saved');

                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index', array('_secure'=>true)));
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('customer/session')
                    ->setAddressFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/edit', array('id'=>$address->getId())));
    }
    
    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);
        
        if ($addressId) {
            $address = Mage::getModel('customer/address')->load($addressId);
            
            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
                Mage::getSingleton('customer/session')
                    ->addError('The address does not belong to this customer');
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
                return;
            }
            
            try {
                $address->delete();
                Mage::getSingleton('customer/session')
                    ->addSuccess('The address has been successfully deleted');
            }
            catch (Exception $e){
                Mage::getSingleton('customer/session')
                    ->addError('There has been an error deleting the address');
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
    }
}// Class Mage_Customer_AccountController END
