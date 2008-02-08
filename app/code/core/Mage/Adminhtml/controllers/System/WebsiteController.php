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
class Mage_Adminhtml_System_WebsiteController extends Mage_Adminhtml_Controller_Action
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
        $id = $this->getRequest()->getParam('website');
        $model = Mage::getModel('core/website');

        if ($id) {
            $model->load($id);
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('admin_current_website', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('adminhtml')->__('Edit Website') : Mage::helper('adminhtml')->__('New Website'), $id ? Mage::helper('adminhtml')->__('Edit Website') : Mage::helper('adminhtml')->__('New Website'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/system_website_edit')->setData('action', Mage::helper('adminhtml')->getUrl('*/system_website/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            Mage::app()->removeCache('config_global');
            $model = Mage::getModel('core/website');
            $model->setData($data);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Website was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $this->_redirect('*/system_config/edit', array('website'=>$model->getCode()));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('website' => $this->getRequest()->getParam('website')));
                return;
            }
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('website');
        $model = Mage::getModel('core/website');
        $model->load($id, 'code');
        if( !$model->getWebsiteId() ) {
            Mage::getSingleton('adminhtml/session')->addError( Mage::helper('adminhtml')->__('Unable to proceed. Please, try again') );
            $this->_redirect('*/*/edit', array('website' => $this->getRequest()->getParam('website')));
            return;
        }

        $this->_initAction()
            ->_addBreadcrumb( Mage::helper('adminhtml')->__('Delete Website'), Mage::helper('adminhtml')->__('Delete Website') )
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/system_website_delete')
                    ->setData('action', Mage::helper('adminhtml')->getUrl('*/system_website/deletePost', array('website' => $id)))
                    ->setWebsiteName($model->getName())
            )
            ->renderLayout();
    }

    public function deletePostAction()
    {
        $id = $this->getRequest()->getParam('website');

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
                $this->_redirect('*/*/edit', array('website' => $this->getRequest()->getParam('website')));
                return;
            }
        }

        $model = Mage::getModel('core/website');
        $model->load($id, 'code');

        try {
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('adminhtml')->__('Website was successfully deleted.') );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError( Mage::helper('adminhtml')->__('Unable to delete Website. Please, try again later.') );
            $this->_redirect('*/*/edit', array('website' => $this->getRequest()->getParam('website')));
            return;
        }

        $this->_redirect('*/system_config');
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('system/config/website');
    }
}
