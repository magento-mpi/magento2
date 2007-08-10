<?php
/**
 * config controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_System_StoreController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('system/config')
            ->_addBreadcrumb(__('System'), __('System'))
        ;
        return $this;
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $id = $this->getRequest()->getParam('store');
        $model = Mage::getModel('core/store');

        if ($id) {
            $model->load($id);
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('admin_current_store', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? __('Edit Store') : __('New Store'), $id ? __('Edit Store') : __('New Store'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/system_store_edit')->setData('action', Mage::getUrl('adminhtml/system_store/save')))
            ->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('core/store');
            $model->setData($data);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Store was saved succesfully'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $this->_redirect('*/system_config/edit', array('store'=>$model->getCode()));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('store'=>$this->getRequest()->getParam('store')));
                return;
            }
        }
    }
    
    public function deleteAction()
    {
        $this->_redirect('*/system_config');        
    }
}
