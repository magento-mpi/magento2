<?php
/**
 * Customer admin controller
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Admin_CustomerController extends Mage_Core_Controller_Front_Action
{
    /**
     * Customers collection JSON
     */
    public function gridDataAction()
    {
        $pageSize = $this->getRequest()->getPost('limit', 30);
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->setPageSize($pageSize);
        
        
        $page = $this->getRequest()->getPost('start', 1);
        if ($page>1) {
            $page = $page/$pageSize+1;
        }
        
        $order = $this->getRequest()->getPost('sort', 'customer_id');
        $dir   = $this->getRequest()->getPost('dir', 'desc');
        $collection->setOrder($order, $dir);
        $collection->setCurPage($page);
        $collection->load();
        
        //$arrGridFields = array('product_id', 'name', 'price', 'description');
        $arrGridFields = array();

        $this->getResponse()->setBody(Zend_Json::encode($collection->toArray($arrGridFields)));
    }
    
    /**
     * Create new customer wizard
     */
    public function wizardAction()
    {
        $block  = Mage::getSingleton('core/layout')->createBlock('admin/customer_wizard');
        $this->getResponse()->setBody($block->getContent());
    }
    
    /**
     * Customer create preview action
     */
    public function createPreviewAction()
    {
        $this->getResponse()->setBody('Customer create preview');
    }
    
    /**
     * Custoner create action
     */
    public function createAction()
    {
        Mage::log('create customer');
        $customerData   = $this->_request->getPost();
        $addressData    = $this->_request->getPost('address');

        $customer = Mage::getModel('customer/customer')->setData($customerData);
        $address  = Mage::getModel('customer/address')->setData($addressData);
        $address->setPrimaryTypes(array_keys($address->getAvailableTypes('address_type_id')));
        
        $customer->addAddress($address);
        $res = array(
            'error' => 0,
        );
        try {
            $customer->save();
            $res['data'] = $customer->toArray();
        }
        catch (Mage_Core_Exception $e) {
            $res['error'] = 1;
            $res['errorMessage'] = 'Customer create error';
            
            if ($messages = $e->getMessages()) {
                $res['errorMessage'] = '';
                foreach ($messages as $message) {
                	$res['errorMessage'].= $message->toHtml();
                }
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
    
    /**
     * Customer card 
     */
    public function cardAction()
    {
        $customerId = $this->getRequest()->getParam('id', false);
        $cardStruct = array();
        
        if ($customerId>0) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $cardStruct['title'] = __('Edit Customer').' "'.$customer->getName().'"';
        }
        else {
            $customerId = false;
            $customer   = false;
            $cardStruct['title'] = __('New Customer');
        }
        
        
        $cardStruct['saveUrl']  = Mage::getUrl('admin', array('controller'=>'customer', 'action'=>'save', 'id'=>$customerId));
        
        $form = new Mage_Admin_Block_Customer_Form($customer);
        $cardStruct['tabs'] = array(
            array(
                'name'  => 'customer_view',
                'title' => __('Customer View'),
                'url'   => Mage::getUrl('admin', array('controller'=>'customer', 'action'=>'view', 'id'=>$customerId)),
                'type'  => 'view'
            ),
            array(
                'name'  => 'general',
                'title' => __('Account Information'),
                'type'  => 'form',
                'background'=>true,
                'form'  => $form->toArray()
            ),
            array(
                'name'  => 'address',
                'type'  => 'address',
                'title' => __('Address List'),
                'storeUrl' => Mage::getUrl('admin', array('controller'=>'customer', 'action'=>'addressList', 'id'=>$customerId)),
                'background'=>true,
            ),
        );
        
        $this->getResponse()->setBody(Zend_Json::encode($cardStruct));
    }
    
    /**
     * Customer view tab content
     */
    public function viewAction()
    {
        $customerId = $this->getRequest()->getParam('id', false);
        $this->getResponse()->setBody('Customer #'.$customerId);
    }
    
    /**
     * Save customer action
     */
    public function saveAction()
    {
        $res = array('error'=>0);
        if ($this->getRequest()->isPost()) {
            $customerId = (int) $this->getRequest()->getParam('id', false);
            $customer   = Mage::getModel('customer/customer')->setData($this->getRequest()->getPost());
            $customer->setCustomerId($customerId);
            
            Mage::log('save customer data');

            try {
                $customer->save();
            }
            catch (Exception $e){
                $res['error'] = 1;
                $res['errorMessage']  = $e->getMessage();
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
    
    /**
     * Delete customer action
     */
    public function deleteAction()
    {
        $customerId = $this->getRequest()->getParam('id', false);
        $customer = Mage::getModel('customer/customer')->setCustomerId($customerId);
        
        $res = array('error' => 0);
        try {
            $customer->delete();
        }
        catch (Exception $e){
            $res['error'] = 1;
            $res['errorMessage'] = 'Customer delete error';
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
    
    /**
     * List of customer addresses
     */
    public function addressListAction()
    {
        $arrRes = array();
        $customerId = $this->getRequest()->getParam('id', false);
        if ($customerId) {
            $addressCollection = Mage::getResourceModel('customer/address_collection')->loadByCustomerId($customerId);
            foreach ($addressCollection as $address) {
                $arrRes[] = array(
                    'address_id'=> $address->getAddressId(),
                    'address'   => nl2br($address->toString("<strong>{{firstname}} {{lastname}}</strong>\n{{street}}\n{{city}}, {{regionName}} {{postcode}}\nT: {{telephone}}"))
                );
            }
        }
        
        $this->getResponse()->setBody(Zend_Json::encode(array('addresses'=>$arrRes)));
    }
    
    /**
     * Customer address form
     */
    public function addressFormAction()
    {
        $addressId = (int) $this->getRequest()->getParam('id');
        $address = Mage::getModel('customer/address');
        if ($addressId>0) {
            $address->load($addressId);
        }
        
        $form = new Mage_Admin_Block_Customer_Address_Form($address);
        $data = array(
            'error' => 0,
            'form' => $form->toArray()
        );
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }    
    /**
     * Save customer address
     */
    public function addressSaveAction()
    {
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer/address')->setData($this->getRequest()->getPost());
            try {
                $address->save();
            }
            catch (Exception $e){
                echo $e;
            }
        }
    }
    
    /**
     * Delete customer address
     */
    public function addressDeleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);
        if ($addressId) {
            $address = Mage::getModel('customer/address')->setAddressId($addressId);
            try {
                $address->delete();
            }
            catch (Exception $e){
                
            }
        }
    }
}