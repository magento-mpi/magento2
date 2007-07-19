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
class Mage_Adminhtml_Customer_GroupController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Customer groups list.
     */
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('customer/group');
        $this->_addBreadcrumb(__('Customers'), __('Customers Title'));
        $this->_addBreadcrumb(__('Customer Groups'), __('Customers Groups Title'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/customer_group', 'group'));

        $this->renderLayout();
    }

    /**
     * Edit or create customer group.
     */
    public function newAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('customer/group');
        $this->_addBreadcrumb(__('Customers'), __('Customers Title'));
        $this->_addBreadcrumb(__('Customer Groups'), __('Customer Groups Title'), Mage::getUrl('adminhtml',array('controller'=>'customer_group')));

        if ($this->getRequest()->getParam('id')) {
            $this->_addBreadcrumb(__('Edit Group'), __('Edit Customer Groups Title'));
        } else {
            $this->_addBreadcrumb(__('New Group'), __('New Customer Groups Title'));
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
//            $this->_helper->redirector->gotoAndExit('','customer_group','adminhtml');
            $this->_redirect('adminhtml/customer_group');
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

//        $this->_helper->redirector->gotoAndExit('','customer_group','adminhtml');
         $this->_redirect('adminhtml/customer_group');
    }
}