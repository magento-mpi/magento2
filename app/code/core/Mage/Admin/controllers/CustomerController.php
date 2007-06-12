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
        $collection = Mage::getModel('customer_resource/customer_collection');
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
    
    public function wizardAction()
    {
        $step = $this->getRequest()->getParam('step', 1);
        
        switch ($step) {
            case 1:
                $customer = Mage::getModel('customer/customer');
                $form = new Mage_Admin_Block_Customer_Form($customer);

                $tab = array(
                    'name'  => 'general',
                    'title' => __('Account Information'),
                    'type'  => 'form',
                    'form'  => $form->toArray()
                );
                $cardStruct['nextPoint']['url'] = Mage::getUrl('admin', array('controller'=>'customer', 'action'=>'wizard', 'step'=>2));
                break;
            case 2:
                $address = Mage::getModel('customer/address');
                $form = new Mage_Admin_Block_Customer_Address_Form($address);

                $tab = array(
                    'name'  => 'general',
                    'title' => __('Customer address'),
                    'type'  => 'form',
                    'form'  => $form->toArray()
                );
                break;
                
        }
        
        $cardStruct['title'] = __('New Customer');
        $cardStruct['error'] = 0;
        $cardStruct['tabs'][] = $tab;
        
        $this->getResponse()->setBody(Zend_Json::encode($cardStruct));
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
    public function save()
    {
        if ($this->getRequest()->isPost()) {
            $customerId = (int) $this->getRequest()->getParam('id', false);
            $customer   = Mage::getModel('customer/customer')->setData($this->getRequest()->getPost());
            $customer->setCustomerId($customerId);
            
            try {
                $customer->save();
            }
            catch (Exception $e){
                
            }
        }
    }
    
    /**
     * Delete customer action
     */
    public function deleteAction()
    {
        $customerId = $this->getRequest()->getParam('id', false);
        if ($customerId) {
            Mage::getModel('customer/customer')->delete($customerId);
        }
    }
    
    /**
     * List of customer addresses
     */
    public function addressListAction()
    {
        $arrRes = array();
        $customerId = $this->getRequest()->getParam('id', false);
        if ($customerId) {
            $addressCollection = Mage::getModel('customer_resource/address_collection')->loadByCustomerId($customerId);
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