<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store controller
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_System_Store extends Magento_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Magento_Adminhtml_Controller_Cms_Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('Magento_Adminhtml::system_store')
            ->_addBreadcrumb(__('System'), __('System'))
            ->_addBreadcrumb(__('Manage Stores'), __('Manage Stores'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title(__('Stores'));
        $this->_initAction()
            ->renderLayout();
    }

    public function newWebsiteAction()
    {
        Mage::register('store_type', 'website');
        $this->_forward('newStore');
    }

    public function newGroupAction()
    {
        Mage::register('store_type', 'group');
        $this->_forward('newStore');
    }

    public function newStoreAction()
    {
        if (!Mage::registry('store_type')) {
            Mage::register('store_type', 'store');
        }
        Mage::register('store_action', 'add');
        $this->_forward('editStore');
    }

    public function editWebsiteAction()
    {
        Mage::register('store_type', 'website');
        $this->_forward('editStore');
    }

    public function editGroupAction()
    {
        Mage::register('store_type', 'group');
        $this->_forward('editStore');
    }

    public function editStoreAction()
    {
        $this->_title(__('Stores'));

        $session = $this->_getSession();
        if ($session->getPostData()) {
            Mage::register('store_post_data', $session->getPostData());
            $session->unsPostData();
        }
        if (!Mage::registry('store_type')) {
            Mage::register('store_type', 'store');
        }
        if (!Mage::registry('store_action')) {
            Mage::register('store_action', 'edit');
        }
        switch (Mage::registry('store_type')) {
            case 'website':
                $itemId     = $this->getRequest()->getParam('website_id', null);
                $model      = Mage::getModel('Magento_Core_Model_Website');
                $title      = __("Web Site");
                $notExists  = __("The website does not exist.");
                $codeBase   = __('Before modifying the website code please make sure that it is not used in index.php.');
                break;
            case 'group':
                $itemId     = $this->getRequest()->getParam('group_id', null);
                $model      = Mage::getModel('Magento_Core_Model_Store_Group');
                $title      = __("Store");
                $notExists  = __("The store does not exist");
                $codeBase   = false;
                break;
            case 'store':
                $itemId     = $this->getRequest()->getParam('store_id', null);
                $model      = Mage::getModel('Magento_Core_Model_Store');
                $title      = __("Store View");
                $notExists  = __("Store view doesn't exist");
                $codeBase   = __('Before modifying the store view code please make sure that it is not used in index.php.');
                break;
        }
        if (null !== $itemId) {
            $model->load($itemId);
        }

        if ($model->getId() || Mage::registry('store_action') == 'add') {
            Mage::register('store_data', $model);

            if (Mage::registry('store_action') == 'add') {
                $this->_title(__('New ') . $title);
            }
            else {
                $this->_title($model->getName());
            }

            if (Mage::registry('store_action') == 'edit' && $codeBase && !$model->isReadOnly()) {
                $this->_getSession()->addNotice($codeBase);
            }

            $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_System_Store_Edit'))
                ->renderLayout();
        }
        else {
            $session->addError($notExists);
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($this->getRequest()->isPost() && $postData = $this->getRequest()->getPost()) {
            if (empty($postData['store_type']) || empty($postData['store_action'])) {
                $this->_redirect('*/*/');
                return;
            }
            $session = $this->_getSession();

            try {
                switch ($postData['store_type']) {
                    case 'website':
                        $postData['website']['name'] = $this->_getHelper()->removeTags($postData['website']['name']);
                        $websiteModel = Mage::getModel('Magento_Core_Model_Website');
                        if ($postData['website']['website_id']) {
                            $websiteModel->load($postData['website']['website_id']);
                        }
                        $websiteModel->setData($postData['website']);
                        if ($postData['website']['website_id'] == '') {
                            $websiteModel->setId(null);
                        }

                        $websiteModel->save();
                        $session->addSuccess(__('The website has been saved.'));
                        break;

                    case 'group':
                        $postData['group']['name'] = $this->_getHelper()->removeTags($postData['group']['name']);
                        $groupModel = Mage::getModel('Magento_Core_Model_Store_Group');
                        if ($postData['group']['group_id']) {
                            $groupModel->load($postData['group']['group_id']);
                        }
                        $groupModel->setData($postData['group']);
                        if ($postData['group']['group_id'] == '') {
                            $groupModel->setId(null);
                        }

                        $groupModel->save();

                        $this->_eventManager->dispatch('store_group_save', array('group' => $groupModel));

                        $session->addSuccess(__('The store has been saved.'));
                        break;

                    case 'store':
                        $eventName = 'store_edit';
                        $storeModel = Mage::getModel('Magento_Core_Model_Store');
                        $postData['store']['name'] = $this->_getHelper()->removeTags($postData['store']['name']);
                        if ($postData['store']['store_id']) {
                            $storeModel->load($postData['store']['store_id']);
                        }
                        $storeModel->setData($postData['store']);
                        if ($postData['store']['store_id'] == '') {
                            $storeModel->setId(null);
                            $eventName = 'store_add';
                        }
                        $groupModel = Mage::getModel('Magento_Core_Model_Store_Group')->load($storeModel->getGroupId());
                        $storeModel->setWebsiteId($groupModel->getWebsiteId());
                        $storeModel->save();

                        Mage::app()->reinitStores();

                        $this->_eventManager->dispatch($eventName, array('store'=>$storeModel));

                        $session->addSuccess(__('The store view has been saved'));
                        break;
                    default:
                        $this->_redirect('*/*/');
                        return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Magento_Core_Exception $e) {
                $this->_getSession()->addMessages($e->getMessages());
                $session->setPostData($postData);
            }
            catch (Exception $e) {
                $session->addException($e, __('An error occurred while saving. Please review the error log.'));
                $session->setPostData($postData);
            }
            $this->_redirectReferer();
            return;
        }
        $this->_redirect('*/*/');
    }

    public function deleteWebsiteAction()
    {
        $this->_title(__('Delete Web Site'));

        $session = $this->_getSession();
        $itemId = $this->getRequest()->getParam('item_id', null);
        if (!$model = Mage::getModel('Magento_Core_Model_Website')->load($itemId)) {
            $session->addError(__('Unable to proceed. Please, try again.'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $session->addError(__('This website cannot be deleted.'));
            $this->_redirect('*/*/editWebsite', array('website_id' => $itemId));
            return ;
        }

        $this->_addDeletionNotice('website');

        $this->_initAction()
            ->_addBreadcrumb(__('Delete Web Site'), __('Delete Web Site'))
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_System_Store_Delete')
                ->setFormActionUrl($this->getUrl('*/*/deleteWebsitePost'))
                ->setBackUrl($this->getUrl('*/*/editWebsite', array('website_id' => $itemId)))
                ->setStoreTypeTitle(__('Web Site'))
                ->setDataObject($model)
            )
            ->renderLayout();
    }

    public function deleteGroupAction()
    {
        $this->_title(__('Delete Store'));

        $session = $this->_getSession();
        $itemId = $this->getRequest()->getParam('item_id', null);
        if (!$model = Mage::getModel('Magento_Core_Model_Store_Group')->load($itemId)) {
            $session->addError(__('Unable to proceed. Please, try again.'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $session->addError(__('This store cannot be deleted.'));
            $this->_redirect('*/*/editGroup', array('group_id' => $itemId));
            return ;
        }

        $this->_addDeletionNotice('store');

        $this->_initAction()
            ->_addBreadcrumb(__('Delete Store'), __('Delete Store'))
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_System_Store_Delete')
                ->setFormActionUrl($this->getUrl('*/*/deleteGroupPost'))
                ->setBackUrl($this->getUrl('*/*/editGroup', array('group_id' => $itemId)))
                ->setStoreTypeTitle(__('Store'))
                ->setDataObject($model)
            )
            ->renderLayout();
    }

    public function deleteStoreAction()
    {
        $this->_title(__('Delete Store View'));

        $session = $this->_getSession();
        $itemId = $this->getRequest()->getParam('item_id', null);
        if (!$model = Mage::getModel('Magento_Core_Model_Store')->load($itemId)) {
            $session->addError(__('Unable to proceed. Please, try again.'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $session->addError(__('This store view cannot be deleted.'));
            $this->_redirect('*/*/editStore', array('store_id' => $itemId));
            return ;
        }

        $this->_addDeletionNotice('store view');;

        $this->_initAction()
            ->_addBreadcrumb(__('Delete Store View'), __('Delete Store View'))
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_System_Store_Delete')
                ->setFormActionUrl($this->getUrl('*/*/deleteStorePost'))
                ->setBackUrl($this->getUrl('*/*/editStore', array('store_id' => $itemId)))
                ->setStoreTypeTitle(__('Store View'))
                ->setDataObject($model)
            )
            ->renderLayout();
    }

    public function deleteWebsitePostAction()
    {
        $itemId = $this->getRequest()->getParam('item_id');

        if (!$model = Mage::getModel('Magento_Core_Model_Website')->load($itemId)) {
            $this->_getSession()->addError(__('Unable to proceed. Please, try again'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $this->_getSession()->addError(__('This website cannot be deleted.'));
            $this->_redirect('*/*/editWebsite', array('website_id' => $model->getId()));
            return ;
        }

        $this->_backupDatabase('*/*/editWebsite', array('website_id' => $itemId));

        try {
            $model->delete();
            $this->_getSession()->addSuccess(__('The website has been deleted.'));
            $this->_redirect('*/*/');
            return ;
        }
        catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, __('Unable to delete website. Please, try again later.'));
        }
        $this->_redirect('*/*/editWebsite', array('website_id' => $itemId));
    }

    public function deleteGroupPostAction()
    {
        $itemId = $this->getRequest()->getParam('item_id');

        if (!$model = Mage::getModel('Magento_Core_Model_Store_Group')->load($itemId)) {
            $this->_getSession()->addError(__('Unable to proceed. Please, try again.'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $this->_getSession()->addError(__('This store cannot be deleted.'));
            $this->_redirect('*/*/editGroup', array('group_id' => $model->getId()));
            return ;
        }

        $this->_backupDatabase('*/*/editGroup', array('group_id' => $itemId));

        try {
            $model->delete();
            $this->_getSession()->addSuccess(__('The store has been deleted.'));
            $this->_redirect('*/*/');
            return ;
        }
        catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, __('Unable to delete store. Please, try again later.'));
        }
        $this->_redirect('*/*/editGroup', array('group_id' => $itemId));
    }

    /**
     * Delete store view post action
     *
     */
    public function deleteStorePostAction()
    {
        $itemId = $this->getRequest()->getParam('item_id');

        if (!$model = Mage::getModel('Magento_Core_Model_Store')->load($itemId)) {
            $this->_getSession()->addError(__('Unable to proceed. Please, try again'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $this->_getSession()->addError(__('This store view cannot be deleted.'));
            $this->_redirect('*/*/editStore', array('store_id' => $model->getId()));
            return ;
        }

        $this->_backupDatabase('*/*/editStore', array('store_id' => $itemId));

        try {
            $model->delete();

            $this->_eventManager->dispatch('store_delete', array('store' => $model));

            $this->_getSession()->addSuccess(__('The store view has been deleted.'));
            $this->_redirect('*/*/');
            return ;
        }
        catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, __('Unable to delete store view. Please, try again later.'));
        }
        $this->_redirect('*/*/editStore', array('store_id' => $itemId));
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::store');
    }

    /**
     * Backup database
     *
     * @param string $failPath redirect path if backup failed
     * @param array $arguments
     * @return Magento_Adminhtml_Controller_System_Store
     */
    protected function _backupDatabase($failPath, $arguments=array())
    {
        if (! $this->getRequest()->getParam('create_backup')) {
            return $this;
        }
        try {
            $backupDb = Mage::getModel('Magento_Backup_Model_Db');
            $backup   = Mage::getModel('Magento_Backup_Model_Backup')
                ->setTime(time())
                ->setType('db')
                ->setPath(Mage::getBaseDir('var') . DS . 'backups');

            $backupDb->createBackup($backup);
            $this->_getSession()->addSuccess(__('The database was backed up.'));
        }
        catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect($failPath, $arguments);
            return ;
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, __('We couldn\'t create a backup right now. Please try again later.'));
            $this->_redirect($failPath, $arguments);
            return ;
        }
        return $this;
    }

    /**
     * Add notification on deleting store / store view / website
     *
     * @param string $typeTitle
     * @return Magento_Adminhtml_Controller_System_Store
     */
    protected function _addDeletionNotice($typeTitle)
    {
        $this->_getSession()->addNotice(
            __('Deleting a %1 will not delete the information associated with the %1 (e.g. categories, products, etc.), but the %1 will not be able to be restored. It is suggested that you create a database backup before deleting the %1.', $typeTitle)
        );
        return $this;
    }

}
