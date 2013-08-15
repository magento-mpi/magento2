<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Controller_System_Design extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title(__('Store Design'));
        $this->loadLayout();
        $this->_setActiveMenu('Mage_Adminhtml::system_design_schedule');
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title(__('Store Design'));

        $this->loadLayout();
        $this->_setActiveMenu('Mage_Adminhtml::system_design_schedule');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $id  = (int) $this->getRequest()->getParam('id');
        $design    = Mage::getModel('Mage_Core_Model_Design');

        if ($id) {
            $design->load($id);
        }

        $this->_title($design->getId() ? __('Edit Store Design Change') : __('New Store Design Change'));

        Mage::register('design', $design);

        $this->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Design_Edit'));
        $this->_addLeft($this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Design_Edit_Tabs', 'design_tabs'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = (int) $this->getRequest()->getParam('id');

            $design = Mage::getModel('Mage_Core_Model_Design');
            if ($id) {
                $design->load($id);
            }

            $design->setData($data['design']);
            if ($id) {
                $design->setId($id);
            }
            try {
                $design->save();

                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(__('You saved the design change.'));
            } catch (Exception $e){
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addError($e->getMessage())
                    ->setDesignData($data);
                $this->_redirect('*/*/edit', array('id'=>$design->getId()));
                return;
            }
        }

        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $design = Mage::getModel('Mage_Core_Model_Design')->load($id);

            try {
                $design->delete();

                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addSuccess(__('You deleted the design change.'));
            } catch (Mage_Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addException($e, __("Cannot delete the design change."));
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/'));
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Adminhtml::design');
    }
}
