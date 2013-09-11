<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Adminhtml\Controller\System;

class Design extends \Magento\Adminhtml\Controller\Action
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
        $design    = \Mage::getModel('\Magento\Core\Model\Design');

        if ($id) {
            $design->load($id);
        }

        $this->_title($design->getId() ? __('Edit Store Design Change') : __('New Store Design Change'));

        \Mage::register('design', $design);

        $this->_addContent($this->getLayout()->createBlock('\Magento\Adminhtml\Block\System\Design\Edit'));
        $this->_addLeft($this->getLayout()->createBlock('\Magento\Adminhtml\Block\System\Design\Edit\Tabs', 'design_tabs'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = (int) $this->getRequest()->getParam('id');

            $design = \Mage::getModel('\Magento\Core\Model\Design');
            if ($id) {
                $design->load($id);
            }

            $design->setData($data['design']);
            if ($id) {
                $design->setId($id);
            }
            try {
                $design->save();

                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You saved the design change.'));
            } catch (\Exception $e){
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')
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
            $design = \Mage::getModel('\Magento\Core\Model\Design')->load($id);

            try {
                $design->delete();

                \Mage::getSingleton('Magento\Adminhtml\Model\Session')
                    ->addSuccess(__('You deleted the design change.'));
            } catch (\Magento\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')
                    ->addError($e->getMessage());
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')
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
