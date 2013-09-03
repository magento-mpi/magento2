<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Manage revision controller
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_VersionsCms_Controller_Adminhtml_Cms_Page_Revision extends Magento_VersionsCms_Controller_Adminhtml_Cms_Page
{
    /**
     * @var Magento_Core_Model_Config_Scope
     */
    protected $_configScope;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Config_Scope $configScope
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Config_Scope $configScope
    ) {
        $this->_configScope = $configScope;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return Magento_VersionsCms_Controller_Adminhtml_Cms_Page_Revision
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('Magento_Cms::cms_page')
            ->_addBreadcrumb(__('CMS'), __('CMS'))
            ->_addBreadcrumb(__('Manage Pages'), __('Manage Pages'))
        ;
        return $this;
    }

    /**
     * Prepare and place revision model into registry
     * with loaded data if id parameter present
     *
     * @param int $revisionId
     * @return Magento_VersionsCms_Model_Page_Revision
     */
    protected function _initRevision($revisionId = null)
    {
        if (is_null($revisionId)) {
            $revisionId = (int) $this->getRequest()->getParam('revision_id');
        }

        $revision = Mage::getModel('Magento_VersionsCms_Model_Page_Revision');
        $userId = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId();
        $accessLevel = Mage::getSingleton('Magento_VersionsCms_Model_Config')->getAllowedAccessLevel();

        if ($revisionId) {
            $revision->loadWithRestrictions($accessLevel, $userId, $revisionId);
        } else {
            // loading empty revision
            $versionId = (int) $this->getRequest()->getParam('version_id');
            $pageId = (int) $this->getRequest()->getParam('page_id');

            // loading empty revision but with general data from page and version
            $revision->loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId);
            $revision->setUserId($userId);
        }

        //setting in registry as cms_page to make work CE blocks
        Mage::register('cms_page', $revision);
        return $revision;
    }

    /**
     * Edit revision of CMS page
     */
    public function editAction()
    {
        $revisionId = $this->getRequest()->getParam('revision_id');
        $revision = $this->_initRevision($revisionId);

        if ($revisionId && !$revision->getId()) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                __('We could not load the specified revision.'));

            $this->_redirect('*/cms_page/edit',
                array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }

        $data = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getFormData(true);
        if (!empty($data)) {
            $_data = $revision->getData();
            $_data = array_merge($_data, $data);
            $revision->setData($_data);
        }

        $this->_initAction()
            ->_addBreadcrumb(__('Edit Revision'),
                __('Edit Revision'));

        $this->renderLayout();
    }

    /**
     * Save action
     *
     * @return Magento_VersionsCms_Controller_Adminhtml_Cms_Page_Revision
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            // init model and set data
            $revision = $this->_initRevision();
            $revision->setData($data)
                ->setUserId(Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId());

            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/' . $this->getRequest()->getParam('back'),
                    array(
                        'page_id' => $revision->getPageId(),
                        'revision_id' => $revision->getId()
                    ));
                return;
            }

            // try to save it
            try {
                // save the data
                $revision->save();

                // display success message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You have saved the revision.'));
                // clear previously saved data from session
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/' . $this->getRequest()->getParam('back'),
                        array(
                            'page_id' => $revision->getPageId(),
                            'revision_id' => $revision->getId()
                        ));
                    return;
                }
                // go to grid
                $this->_redirect('*/cms_page_version/edit', array(
                        'page_id' => $revision->getPageId(),
                        'version_id' => $revision->getVersionId()
                    ));
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit',
                    array(
                        'page_id' => $this->getRequest()->getParam('page_id'),
                        'revision_id' => $this->getRequest()->getParam('revision_id'),
                        ));
                return;
            }
        }
        return $this;
    }

    /**
     * Publishing revision
     */
    public function publishAction()
    {
        $revision = $this->_initRevision();

        try {
            $revision->publish();
            // display success message
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You have published the revision.'));
            $this->_redirect('*/cms_page/edit', array('page_id' => $revision->getPageId()));
            return;
        } catch (Exception $e) {
            // display error message
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            // redirect to edit form
            $this->_redirect('*/*/edit', array(
                    'page_id' => $this->getRequest()->getParam('page_id'),
                    'revision_id' => $this->getRequest()->getParam('revision_id')
                    ));
            return;
        }
    }

    /**
     * Prepares page with iframe
     *
     * @return Magento_VersionsCms_Controller_Adminhtml_Cms_Page_Revision
     */
    public function previewAction()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if (empty($data) || !isset($data['page_id'])) {
            $this->_forward('noRoute');
            return $this;
        }

        $page = $this->_initPage();
        $this->loadLayout();

        $stores = $page->getStoreId();
        if (isset($data['stores'])) {
            $stores = $data['stores'];
        }

        /*
         * Checking if all stores passed then we should not assign array to block
         */
        $allStores = false;
        if (is_array($stores) && count($stores) == 1 && !array_shift($stores)) {
            $allStores = true;
        }

        if (!$allStores) {
            $this->getLayout()->getBlock('store_switcher')->setStoreIds($stores);
        }

        // Setting default values for selected store and revision
        $data['preview_selected_store'] = 0;
        $data['preview_selected_revision'] = 0;

        $this->getLayout()->getBlock('preview_form')->setFormData($data);

        // Remove revision switcher if page is out of version control
        if (!$page->getUnderVersionControl()) {
            $this->getLayout()->unsetChild('tools', 'revision_switcher');
        }

        $this->renderLayout();
    }

    /**
     * Generates preview of page
     *
     * @return Magento_VersionsCms_Controller_Adminhtml_Cms_Page_Revision
     */
    public function dropAction()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if (!empty($data) && isset($data['page_id'])) {
            // init model and set data
            $page = Mage::getSingleton('Magento_Cms_Model_Page')
                ->load($data['page_id']);
            if (!$page->getId()) {
                $this->_forward('noRoute');
                return $this;
            }

            /*
             * If revision was selected load it and get data for preview from it
             */
            $_tempData = null;
            if (isset($data['preview_selected_revision']) && $data['preview_selected_revision']) {
                $revision = $this->_initRevision($data['preview_selected_revision']);
                if ($revision->getId()) {
                    $_tempData = $revision->getData();
                }
            }

            /*
             * If there was no selected revision then use posted data
             */
            if (is_null($_tempData)) {
                $_tempData = $data;
            }

            /*
             * Posting posted data in page model
             */
            $page->addData($_tempData);

            /*
             * Retrieve store id from page model or if it was passed from post
             */
            $selectedStoreId = $page->getStoreId();
            if (is_array($selectedStoreId)) {
                $selectedStoreId = array_shift($selectedStoreId);
            }

            if (isset($data['preview_selected_store']) && $data['preview_selected_store']) {
                $selectedStoreId = $data['preview_selected_store'];
            } else {
                if (!$selectedStoreId) {
                    $selectedStoreId = Mage::app()->getDefaultStoreView()->getId();
                }
            }
            $selectedStoreId = (int) $selectedStoreId;

            /*
             * Emulating front environment
             */
            Mage::app()->getLocale()->emulate($selectedStoreId);
            Mage::app()->setCurrentStore(Mage::app()->getStore($selectedStoreId));

            $theme = Mage::getDesign()->getConfigurationDesignTheme(null, array('store' => $selectedStoreId));
            Mage::getDesign()->setDesignTheme($theme, 'frontend');

            $designChange = Mage::getSingleton('Magento_Core_Model_Design')
                ->loadChange($selectedStoreId);

            if ($designChange->getData()) {
                Mage::getDesign()->setDesignTheme($designChange->getDesign());
            }

            // add handles used to render cms page on frontend
            $this->getLayout()->getUpdate()->addHandle('default');
            $this->getLayout()->getUpdate()->addHandle('cms_page_view');
            Mage::helper('Magento_Cms_Helper_Page')->renderPageExtended($this);
            Mage::app()->getLocale()->revert();

        } else {
            $this->_forward('noRoute');
        }

        return $this;
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('revision_id')) {
            $error = false;
            try {
                // init model and delete
                $revision = $this->_initRevision();
                $revision->delete();
                // display success message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You have deleted the revision.'));
                $this->_redirect('*/cms_page_version/edit', array(
                        'page_id' => $revision->getPageId(),
                        'version_id' => $revision->getVersionId()
                    ));
                return;
            } catch (Magento_Core_Exception $e) {
                // display error message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $error = true;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('Something went wrong while deleting the revision.'));
                $error = true;
            }

            // go back to edit form
            if ($error) {
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }
        // display error message
        Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__("We can't find a revision to delete."));
        // go to grid
        $this->_redirect('*/cms_page/edit', array('_current' => true));
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'save':
                return Mage::getSingleton('Magento_VersionsCms_Model_Config')->canCurrentUserSaveRevision();
                break;
            case 'publish':
                return Mage::getSingleton('Magento_VersionsCms_Model_Config')->canCurrentUserPublishRevision();
                break;
            case 'delete':
                return Mage::getSingleton('Magento_VersionsCms_Model_Config')->canCurrentUserDeleteRevision();
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Cms::page');
                break;
        }
    }

    /**
     * Controller predispatch method
     *
     * @return Magento_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        if ($this->getRequest()->getActionName() == 'drop') {
            $this->_configScope->setCurrentScope('frontend');
        }
        parent::preDispatch();
    }

    /**
     * New Revision action
     *
     * @return Magento_VersionsCms_Controller_Adminhtml_Cms_Page_Revision
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
}
