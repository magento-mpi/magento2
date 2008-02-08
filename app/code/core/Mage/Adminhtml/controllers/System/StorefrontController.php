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
 * Storefront controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_Adminhtml_System_StorefrontController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        return $this;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/storefront');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Storefront'), Mage::helper('adminhtml')->__('Storefront'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_storefront_storefront'));
//        $this->_addJs($this->getLayout()->createBlock('core/template')
//            ->setTemplate('system/storefront_js.phtml')
//            ->setUrlEditWebsite(Mage::getUrl('*/*/editWebsite/', array('website_id'=>'#{item_id}')))
//            ->setUrlEditGroup(Mage::getUrl('*/*/editGroup/', array('group_id'=>'#{item_id}')))
//            ->setUrlEditStore(Mage::getUrl('*/*/editStore/', array('store_id'=>'#{item_id}')))
//        );
        $this->renderLayout();
    }

    public function newWebsiteAction()
    {
        Mage::register('storefront_type', 'website');
        $this->_forward('newStore');
    }

    public function newGroupAction()
    {
        Mage::register('storefront_type', 'group');
        $this->_forward('newStore');
    }

    public function newStoreAction()
    {
        if (!Mage::registry('storefront_type')) {
            Mage::register('storefront_type', 'store');
        }
        Mage::register('storefront_action', 'add');
        $this->_forward('editStore');
    }

    public function editWebsiteAction()
    {
        Mage::register('storefront_type', 'website');
        $this->_forward('editStore');
    }

    public function editGroupAction()
    {
        Mage::register('storefront_type', 'group');
        $this->_forward('editStore');
    }

    public function editStoreAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        if ($session->getPostData()) {
            Mage::register('storefront_post_data', $session->getPostData());
            $session->unsPostData();
        }
        if (!Mage::registry('storefront_type')) {
            Mage::register('storefront_type', 'store');
        }
        if (!Mage::registry('storefront_action')) {
            Mage::register('storefront_action', 'edit');
        }
        switch (Mage::registry('storefront_type')) {
            case 'website':
                $itemId     = $this->getRequest()->getParam('website_id');
                $model      = Mage::getModel('core/website')->load($itemId);
                $notExists  = Mage::helper('core')->__('Website not exists');
                break;
            case 'group':
                $itemId     = $this->getRequest()->getParam('group_id');
                $model      = Mage::getModel('core/store_group')->load($itemId);
                $notExists  = Mage::helper('core')->__('Store group not exists');
                break;
            case 'store':
                $itemId     = $this->getRequest()->getParam('store_id');
                $model      = Mage::getModel('core/store')->load($itemId);
                $notExists  = Mage::helper('core')->__('Store not exists');
                break;
        }

        if ($model->getId() || Mage::registry('storefront_action') == 'add') {
            Mage::register('storefront_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('system/storefront');
            $this->_addContent($this->getLayout()
                ->createBlock('adminhtml/system_storefront_edit')
                ->setData('action', Mage::getUrl('*/*/save')));

            $this->renderLayout();
        }
        else {
            $session->addError($notExists);
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($this->getRequest()->isPost() && $postData = $this->getRequest()->getPost()) {
            if (empty($postData['storefront_type']) || empty($postData['storefront_action'])) {
                $this->_redirect('*/*/');
                return;
            }
            $session = Mage::getSingleton('adminhtml/session');
            /* @var $session Mage_Adminhtml_Model_Session */

            try {
                switch ($postData['storefront_type']) {
                    case 'website':
                        $websiteModel = Mage::getModel('core/website')->setData($postData['website']);
                        if ($postData['storefront_action'] == 'add') {
                            $groupModel = Mage::getModel('core/store_group')->setData($postData['group']);
                            $storeModel = Mage::getModel('core/store')->setData($postData['store']);

                            $groupModel->addStore($storeModel);
                            $websiteModel->addGroup($groupModel);
                        }
                        $websiteModel->save();
                        $session->addSuccess(Mage::helper('core')->__('Website was successfully saved'));
                        break;

                    case 'group':
                        $groupModel = Mage::getModel('core/store_group')->setData($postData['group']);
                        if ($postData['storefront_action'] == 'add') {
                            $storeModel = Mage::getModel('core/store')->setData($postData['store']);
                            $groupModel->addStore($storeModel);
                        }
                        $groupModel->save();
                        $session->addSuccess(Mage::helper('core')->__('Store group was successfully saved'));
                        break;

                    case 'store':
                        $storeModel = Mage::getModel('core/store')->setData($postData['store']);
                        $groupModel = Mage::getModel('core/store_group')->load($storeModel->getGroupId());
                        $storeModel->setWebsiteId($groupModel->getWebsiteId());
                        $storeModel->save();
                        $session->addSuccess(Mage::helper('core')->__('Store was successfully saved'));
                        break;
                    default:
                        $this->_redirect('*/*/');
                        return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
                $session->setPostData($postData);
            }
            catch (Exception $e) {
                $session->addException($e, Mage::helper('core')->__('Error while saving. Please try again later.'));
                $session->setPostData($postData);
            }
            $this->_redirectReferer();
            return;
        }
        $this->_redirect('*/*/');
    }

    public function deleteWebsiteAction()
    {

    }

    public function deleteGroupAction()
    {

    }

    public function deleteStoreAction()
    {

    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/storefront');
    }
}