<?php
/**
 * Customer groups controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Customer_GroupController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Customer groups list.
     */
    public function indexAction() 
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/group');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'), Mage::getUrl('adminhtml',array('controller'=>'customer')))
            ->addLink(__('customers groups'), __('customers groups title'));
        
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('adminhtml/customer_group', 'group'));
        
        $this->renderLayout();
    }
    
    /**
     * Edit or create customer group.
     */
    public function newAction() 
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/group');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'), Mage::getUrl('adminhtml',array('controller'=>'customer')))
            ->addLink(__('customer groups'), __('customer groups title'), Mage::getUrl('adminhtml',array('controller'=>'customer_group')));
            
        if ($this->getRequest()->getParam('id')) {
            $this->getLayout()->getBlock('breadcrumbs')
                ->addLink(__('edit customer group'), __('edit customer groups title'));
        } else {
            $this->getLayout()->getBlock('breadcrumbs')
                ->addLink(__('new customer group'), __('new customer groups title'));
        }
        
        $this->getLayout()->getBlock('content')
            ->append($this->getLayout()->createBlock('adminhtml/customer_group_edit', 'group')
                        ->setEditMode((bool)$this->getRequest()->getParam('id')));
        
        $this->renderLayout();
    }
    
    /**
     * Edit customer group action. Forward to new action.
     */
    public function editAction()
    {
        $this->_forward('new');
    }
    
    /**
     * Create or save customer group.
     */
    public function saveAction()
    {
        $customerGroup = Mage::getModel('customer/group');
        if ($id = (int)$this->getRequest()->getParam('id')) {
            $customerGroup->load($id);
        }
        
        if ($code = $this->getRequest()->getParam('code')) {
            $customerGroup->setCode($code);
            $customerGroup->save();
            $this->_helper->redirector->gotoAndExit('','customer_group','adminhtml');        
        } else {
            $this->_forward('new');
        }
                
    }
    
    /**
     * Delete customer group action
     */
    public function deleteAction()
    {
        $customerGroup = Mage::getModel('customer/group');
        if ($id = (int)$this->getRequest()->getParam('id')) {
            $customerGroup->load($id);
            $customerGroup->delete();
        }
        
        $this->_helper->redirector->gotoAndExit('','customer_group','adminhtml');
    }
}
