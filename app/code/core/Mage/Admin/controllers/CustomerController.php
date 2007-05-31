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
        $collection = Mage::getModel('customer_resource', 'customer_collection');
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
     * Customer card 
     */
    public function cardAction()
    {
        $customerId = $this->getRequest()->getParam('id', false);
        $cardStruct = array();
        $cardStruct['title']    = 'Customer card';
        $cardStruct['saveUrl']  = Mage::getUrl('admin', array('controller'=>'customer', 'action'=>'save', 'id'=>$customerId));
        
        $form = new Mage_Admin_Block_Customer_Form();
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
                'storeUrl' => Mage::getBaseUrl().'admin/address/gridData/id/'.$customerId.'/',
                'background'=>true,
            ),
        );
        
        $this->getResponse()->setBody(Zend_Json::encode($cardStruct));
    }
    
    public function viewAction()
    {
        $customerId = $this->getRequest()->getParam('id', false);
        $this->getResponse()->setBody('Customer #'.$customerId);
    }

    /**
     * Customer form
     */
    public function formAction()
    {
        $customerId = $this->getRequest()->getParam('id', false);
        $customer = Mage::getModel('customer', 'customer')->load($customerId);

        $form = $this->getLayout()->createBlock('form', 'customer.form');
        $form->setTemplate('form.phtml');
        
        $form->setAttribute('legend', 'Account information');
        $form->setAttribute('class', 'x-form');
        $form->setAttribute('action', Mage::getBaseUrl().'admin/customer/formPost/');
        
            
        
        $this->getResponse()->setBody($form->toHtml());
    }

    public function formPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer', 'customer')->setData($this->getRequest()->getPost());
            
            try {
                $customer->save();
            }
            catch (Exception $e){
                
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