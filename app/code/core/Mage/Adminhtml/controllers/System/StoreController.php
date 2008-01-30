<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * config controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_System_StoreController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/config')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'))
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
            ->_addBreadcrumb($id ? Mage::helper('adminhtml')->__('Edit Store View') : Mage::helper('adminhtml')->__('New Store View'), $id ? Mage::helper('adminhtml')->__('Edit Store View') : Mage::helper('adminhtml')->__('New Stores View'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/system_store_edit')->setData('action', Mage::getUrl('*/system_store/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            Mage::app()->removeCache('config_global');
            $model = Mage::getModel('core/store');
            $model->setData($data);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Store View was successfully saved'));
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
        $id = $this->getRequest()->getParam('store');
        $model = Mage::getModel('core/store');
        $model->load($id, 'code');
        if( !$model->getStoreId() ) {
            Mage::getSingleton('adminhtml/session')->addError( Mage::helper('adminhtml')->__('Unable to proceed. Please, try again') );
            $this->_redirect('*/*/edit', array('store' => $this->getRequest()->getParam('store')));
            return;
        }

        $this->_initAction()
            ->_addBreadcrumb( Mage::helper('adminhtml')->__('Delete Store'), Mage::helper('adminhtml')->__('Delete Store') )
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/system_store_delete')
                    ->setData('action', Mage::getUrl('*/system_store/deletePost', array('store' => $id)))
                    ->setStoreName($model->getName())
            )
            ->renderLayout();
    }

    public function deletePostAction()
    {
        $id = $this->getRequest()->getParam('store');

        if( $this->getRequest()->getParam('create_backup') ) {
            $backup = Mage::getModel('backup/backup')
                    ->setTime(time())
                    ->setType('db')
                    ->setPath(Mage::getBaseDir("var") . DS . "backups");

            try {
    	        $dbDump = Mage::getModel('backup/db')->renderSql();
	            $backup->setFile($dbDump);
	            Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('adminhtml')->__('Database was successfuly backed up.') );
            }
            catch (Exception  $e) {
                Mage::getSingleton('adminhtml/session')->addError( Mage::helper('adminhtml')->__('Unable to create backup. Please, try again later.') );
                $this->_redirect('*/*/edit', array('store' => $this->getRequest()->getParam('store')));
                return;
            }
        }

        $model = Mage::getModel('core/store');
        $model->load($id, 'code');

        try {
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('adminhtml')->__('Store was successfully deleted.') );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError( Mage::helper('adminhtml')->__('Unable to delete Store. Please, try again later.') );
            $this->_redirect('*/*/edit', array('store' => $this->getRequest()->getParam('store')));
            return;
        }

        $this->_redirect('*/system_config');
    }


    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('system/config/store');
    }
}
