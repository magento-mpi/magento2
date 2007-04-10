<?php
/**
 * Customer admin controller
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_CustomerController extends Mage_Core_Controller_Admin_Action
{
    /**
     * Customers collection JSON
     */
    public function gridDataAction()
    {
        $pageSize = $this->getRequest()->getPost('limit', 30);
        $collection = Mage::getModel('customer', 'customer_collection');
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

        $this->getResponse()->setBody(Zend_Json::encode($collection->__toArray($arrGridFields)));
    }
    
    /**
     * Customer card tabs
     */
    public function cardAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $cardStruct = array();
        $cardStruct['tabs'] = array(
            0 => array(
                'name' => 'general',
                'title' => __('General Information'),
                'url' => Mage::getBaseUrl().'/mage_customer/customer/form/id/'.$customerId.'/',
                'type' => 'form',
                'active' => true
            ),
            1 => array(
                'type' => 'address',
			    'active' => false
            ),
        );
        
        $this->getResponse()->setBody(Zend_Json::encode($cardStruct));
    }

    /**
     * Customer form
     */
    public function formAction()
    {
        $customerId = $this->getRequest()->getParam('id', false);
        $customer = Mage::getModel('customer', 'customer');
        $customer->load($customerId);
        
        $form = Mage::createBlock('form', 'customer.form');
        $form->setViewName('Mage_Core', 'form.phtml');
        
        $form->setAttribute('legend', 'Customer information');
        $form->setAttribute('class', 'x-form');
        $form->setAttribute('action', Mage::getBaseUrl().'/mage_customer/customer/formPost/');
        
        $form->addField(
            'customer_id', 
            'hidden', 
            array (
                'name'=>'customer_id',
            	'value' => $customerId
            )
        );
                
        $form->addField(
            'customer_firstname', 
            'text', 
            array(
                'name'  => 'customer_firstname',
                'label' => __('Firstname'),
                'id'    => 'customer_firstname',
                'title' => __('Customer Firstname'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField(
            'customer_lastname', 
            'text', 
            array(
                'name'  => 'customer_lastname',
                'label' => __('Lastname'),
                'id'    => 'customer_lastname',
                'title' => __('Customer Lastname'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField(
            'customer_email', 
            'text', 
            array(
            'name'  => 'customer_email',
                'label' => __('Email'),
                'id'    => 'customer_email',
                'title' => __('Customer Email'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

       $form->setElementsValues($customer->__toArray());
            
       $form->addField(
            'customer_pass', 
            'password', 
            array(
                'name'  => 'customer_pass',
                'label' => __('Password'),
                'id'    => 'customer_pass',
                'title' => __('Customer Password'),
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );
        
        $this->getResponse()->setBody($form->toString());
    }

    public function formPostAction()
    {
        /*$customerId = $this->getRequest()->getPost('customer_id', 0);
        $customer = Mage::getModel('customer', 'customer', array($customerId));
        $customer->setEmail($this->getRequest()->getPost('customer_email', false));
        $customer->setFirstName($this->getRequest()->getPost('customer_firstname', false));
        $customer->setLastName($this->getRequest()->getPost('customer_lasttname', false));
        $customer->save();*/
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer', 'customer');
            $customer->setData($this->getRequest()->getPost());
            
            if ($customer->validateSave() && $customer->save()) {

            }
        }
    }
    
    public function deleteAction()
    {
        $customerId = $this->getRequest()->getParam('id', false);
        if ($customerId) {
            Mage::getModel('customer', 'customer')->delete($customerId);
        }
    }
}