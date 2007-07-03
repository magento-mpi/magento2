<?php
/**
 * Customer admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_CustomerController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Customers list action
     */
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        
        $this->getLayout()->getBlock('menu')->setActive('customer');
        //$this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/customer_left'));
        
        $block = $this->getLayout()->createBlock('adminhtml/customers', 'customers');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'));

        $this->renderLayout();
    }
    
    /**
     * Customer view action
     */
    public function editAction()
    {
        $customerId = $this->getRequest()->getParam('id');
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/new');
            
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('adminhtml/customer_edit')
        );
        
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'), Mage::getUrl('adminhtml/customer'));
        
        if ($customerId) {
            $breadcrumbs->addLink(__('customer').' #'.$customerId, __('customer').' #'.$customerId);
        }
        else {
            $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/store_switcher'));            
            $breadcrumbs->addLink(__('new customer'), __('new customer title'));
        }
        
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/customer_tabs'));
        
        $this->renderLayout();
    }
    
    /**
     * Create new customer action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function deleteAction()
    {
        $customerId = $this->getRequest()->getParam('id');
        if ($customerId) {
            try {
                $customer = Mage::getModel('customer/customer')
                    ->setId($customerId)
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addMessage();
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addMessage();
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('adminhtml/customer'));
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $customer = Mage::getModel('customer/customer');
            //$customer->setData($data);
            if ($customerId = $this->getRequest()->getParam('id')) {
                $customer->setId($customerId);
            }
            if (isset($data['address'])) {
                if (isset($data['address']['_template_'])) {
                    unset($data['address']['_template_']);
                }
                foreach ($data['address'] as $index => $addressData) {
                    $address = Mage::getModel('customer/address');
                    $address->setData($addressData);
                    if ($index = (int) $index) {
                        $address->setId($index);
                    }
                	$customer->addAddress($address);
                }
                unset($data['address']);
            }
            $customer->addData($data);
            
            try {
                $customer->save();
            }
            catch (Exception $e){
                echo $e;
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('adminhtml/customer'));
    }

    public function onlineAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/online');
        $block = $this->getLayout()->createBlock('adminhtml/customer_online', 'customer_online');
        $this->getLayout()->getBlock('content')->append($block);

        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'));

        $this->renderLayout();


        $collection = Mage::getResourceSingleton('log/visitor_collection')
            ->useOnlineFilter()
            ->load();

        foreach ($collection->getItems() as $item) {
        	$item->addIpData($item)
                 ->addCustomerData($item)
        	     ->addQuoteData($item);
        }
    }
    
 /*   
    public function groupAction() 
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/group');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'), Mage::getUrl('adminhtml',array('controller'=>'customer')))
            ->addLink(__('customers groups'), __('customers groups title'));
            
        $this->renderLayout();
    }
    
    public function groupNewAction() 
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/group');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'), Mage::getUrl('adminhtml',array('controller'=>'customer')))
            ->addLink(__('customer groups'), __('customer groups title'), Mage::getUrl('adminhtml',array('controller'=>'customer','action'=>'group')))
            ->addLink(__('new customer group'), __('new customer groups title'));
            
        $this->renderLayout();
    }*/
}
