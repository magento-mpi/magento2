<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Adminhtml_Controller_System_Design extends Magento_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title(__('Store Design'));
        $this->loadLayout();
        $this->_setActiveMenu('Magento_Adminhtml::system_design_schedule');
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
        $this->_setActiveMenu('Magento_Adminhtml::system_design_schedule');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $id  = (int) $this->getRequest()->getParam('id');
        $design    = Mage::getModel('Magento_Core_Model_Design');

        if ($id) {
            $design->load($id);
        }

        $this->_title($design->getId() ? __('Edit Store Design Change') : __('New Store Design Change'));

        Mage::register('design', $design);

        $this->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_System_Design_Edit'));
        $this->_addLeft($this->getLayout()->createBlock('Magento_Adminhtml_Block_System_Design_Edit_Tabs', 'design_tabs'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = (int) $this->getRequest()->getParam('id');

            $design = Mage::getModel('Magento_Core_Model_Design');
            if ($id) {
                $design->load($id);
            }

            $design->setData($data['design']);
            if ($id) {
                $design->setId($id);
            }
            try {
                $design->save();

                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You saved the design change.'));
            } catch (Exception $e){
                Mage::getSingleton('Magento_Adminhtml_Model_Session')
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
            $design = Mage::getModel('Magento_Core_Model_Design')->load($id);

            try {
                $design->delete();

                Mage::getSingleton('Magento_Adminhtml_Model_Session')
                    ->addSuccess(__('You deleted the design change.'));
            } catch (\Magento\MagentoException $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')
                    ->addException($e, __("Cannot delete the design change."));
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/'));
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::design');
    }
}
