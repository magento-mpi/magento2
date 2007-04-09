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
        $pageSize = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $collection = Mage::getResourceModel('customer', 'customer_collection');
        $collection->setPageSize($pageSize);
        
        
        $page = isset($_POST['start']) ? $_POST['start']/$pageSize+1 : 1;
        
        $order = isset($_POST['sort']) ? $_POST['sort'] : 'customer_id';
        $dir   = isset($_POST['dir']) ? $_POST['dir'] : 'desc';
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
        $cardStruct = array();
        $cardStruct['tabs'] = array(
            0 => array(
                'name' => 'general',
                'title' => 'General Information',
                'url' => Mage::getBaseUrl().'/mage_customer/customer/form/',
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
        $customerId = $this->getRequest()->getParam('customer', false);
        $customer = Mage::getResourceModel('customer', 'customer');
        $customer->load($customerId);
        
        $form = Mage::createBlock('form', 'customer.form');
        $form->setViewName('Mage_Core', 'form.phtml');
        
        $form->setAttribute('legend', 'Customer information');
        $form->setAttribute('class', 'x-form');
        $form->setAttribute('action', Mage::getBaseUrl().'/mage_customer/customer/formPost/');
        
        $form->addField(
            'customer_id', 
            'hidden', 
            array(
                'name'=>'customer_id'
            )
        );
                
        $form->addField(
            'customer_firstname', 
            'text', 
            array(
                'name'  => 'customer_firstname',
                'label' => 'Firstname',
                'id'    => 'customer_firstname',
                'title' => 'Customer Firstname',
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField(
            'customer_lastname', 
            'text', 
            array(
                'name'  => 'customer_lastname',
                'label' => 'Lastname',
                'id'    => 'customer_lastname',
                'title' => 'Customer Lastname',
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );

        $form->addField(
            'customer_email', 
            'text', 
            array(
                'name'  => 'customer_email',
                'label' => 'Email',
                'id'    => 'customer_email',
                'title' => 'Customer Email',
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
                'label' => 'Password',
                'id'    => 'customer_pass',
                'title' => 'Customer Password',
                'validation'=> '',
                'ext_type'  => 'TextField'
            )
        );
        
        $this->getResponse()->setBody($form->toString());
    }

    public function formPostAction()
    {
        
    }
    
    public function deleteAction()
    {
        $customerId = $this->getRequest()->getParam('customer', false);
        if ($customerId) {
            Mage::getResourceModel('customer', 'customer')->delete($customerId);
        }
    }
}