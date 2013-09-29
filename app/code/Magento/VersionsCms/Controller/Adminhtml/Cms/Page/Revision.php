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
 */
class Magento_VersionsCms_Controller_Adminhtml_Cms_Page_Revision
    extends Magento_VersionsCms_Controller_Adminhtml_Cms_Page
{
    /**
     * @var Magento_Core_Model_Config_Scope
     */
    protected $_configScope;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Cms_Model_Page
     */
    protected $_cmsPage;

    /**
     * @var Magento_Core_Model_Design
     */
    protected $_design;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Config_Scope $configScope
     * @param Magento_VersionsCms_Model_Config $cmsConfig
     * @param Magento_Backend_Model_Auth_Session $backendAuthSession
     * @param Magento_VersionsCms_Model_Page_Version $pageVersion
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Cms_Model_Page $cmsPage
     * @param Magento_Core_Model_Design $design
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Config_Scope $configScope,
        Magento_VersionsCms_Model_Config $cmsConfig,
        Magento_Backend_Model_Auth_Session $backendAuthSession,
        Magento_VersionsCms_Model_Page_Version $pageVersion,
        Magento_Cms_Model_PageFactory $pageFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Cms_Model_Page $cmsPage,
        Magento_Core_Model_Design $design
    ) {
        $this->_configScope = $configScope;
        $this->_storeManager = $storeManager;
        $this->_locale = $locale;
        $this->_cmsPage = $cmsPage;
        $this->_design = $design;
        parent::__construct($context, $coreRegistry, $cmsConfig, $backendAuthSession, $pageVersion, $pageFactory);
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
            ->_addBreadcrumb(__('Manage Pages'), __('Manage Pages'));
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
            $revisionId = (int)$this->getRequest()->getParam('revision_id');
        }

        $revision = $this->_objectManager->create('Magento_VersionsCms_Model_Page_Revision');
        $userId = $this->_backendAuthSession->getUser()->getId();
        $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();

        if ($revisionId) {
            $revision->loadWithRestrictions($accessLevel, $userId, $revisionId);
        } else {
            // loading empty revision
            $versionId = (int)$this->getRequest()->getParam('version_id');
            $pageId = (int)$this->getRequest()->getParam('page_id');

            // loading empty revision but with general data from page and version
            $revision->loadByVersionPageWithRestrictions($versionId, $pageId, $accessLevel, $userId);
            $revision->setUserId($userId);
        }

        //setting in registry as cms_page to make work CE blocks
        $this->_coreRegistry->register('cms_page', $revision);
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
            $this->_session->addError(__('We could not load the specified revision.'));

            $this->_redirect('*/cms_page/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $_data = $revision->getData();
            $_data = array_merge($_data, $data);
            $revision->setData($_data);
        }

        $this->_initAction()->_addBreadcrumb(__('Edit Revision'), __('Edit Revision'));
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
        $data = $this->getRequest()->getPost();
        if ($data) {
            $data = $this->_filterPostData($data);
            // init model and set data
            $revision = $this->_initRevision();
            $revision->setData($data)
                ->setUserId($this->_backendAuthSession->getUser()->getId());

            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/' . $this->getRequest()->getParam('back'), array(
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
                $this->_session->addSuccess(__('You have saved the revision.'));
                // clear previously saved data from session
                $this->_session->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/' . $this->getRequest()->getParam('back'), array(
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
                $this->_session->addError($e->getMessage());
                // save data in session
                $this->_session->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array(
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
            $this->_session->addSuccess(__('You have published the revision.'));
            $this->_redirect('*/cms_page/edit', array('page_id' => $revision->getPageId()));
            return;
        } catch (Exception $e) {
            // display error message
            $this->_session->addError($e->getMessage());
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
            $page = $this->_cmsPage->load($data['page_id']);
            if (!$page->getId()) {
                $this->_forward('noRoute');
                return $this;
            }

            /**
             * If revision was selected load it and get data for preview from it
             */
            $_tempData = null;
            if (isset($data['preview_selected_revision']) && $data['preview_selected_revision']) {
                $revision = $this->_initRevision($data['preview_selected_revision']);
                if ($revision->getId()) {
                    $_tempData = $revision->getData();
                }
            }

            /**
             * If there was no selected revision then use posted data
             */
            if (is_null($_tempData)) {
                $_tempData = $data;
            }

            /**
             * Posting posted data in page model
             */
            $page->addData($_tempData);

            /**
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
                    $selectedStoreId = $this->_storeManager->getDefaultStoreView()->getId();
                }
            }
            $selectedStoreId = (int)$selectedStoreId;

            /**
             * Emulating front environment
             */
            $this->_locale->emulate($selectedStoreId);
            $this->_storeManager->setCurrentStore($this->_storeManager->getStore($selectedStoreId));

            $theme = $this->_objectManager->get('Magento_Core_Model_View_DesignInterface')
                ->getConfigurationDesignTheme(null, array('store' => $selectedStoreId));
            $this->_objectManager->get('Magento_Core_Model_View_DesignInterface')->setDesignTheme($theme, 'frontend');

            $designChange = $this->_design->loadChange($selectedStoreId);

            if ($designChange->getData()) {
                $this->_objectManager->get('Magento_Core_Model_View_DesignInterface')
                    ->setDesignTheme($designChange->getDesign());
            }

            // add handles used to render cms page on frontend
            $this->getLayout()->getUpdate()->addHandle('default');
            $this->getLayout()->getUpdate()->addHandle('cms_page_view');
            $this->_objectManager->get('Magento_Cms_Helper_Page')->renderPageExtended($this);
            $this->_locale->revert();

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
        $id = $this->getRequest()->getParam('revision_id');
        if ($id) {
            try {
                // init model and delete
                $revision = $this->_initRevision();
                $revision->delete();
                // display success message
                $this->_session->addSuccess(__('You have deleted the revision.'));
                $this->_redirect('*/cms_page_version/edit', array(
                        'page_id' => $revision->getPageId(),
                        'version_id' => $revision->getVersionId()
                    ));
                return;
            } catch (Magento_Core_Exception $e) {
                // display error message
                $this->_session->addError($e->getMessage());
                $error = true;
            } catch (Exception $e) {
                $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
                $this->_session->addError(__('Something went wrong while deleting the revision.'));
                $error = true;
            }

            // go back to edit form
            if ($error) {
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }
        // display error message
        $this->_session->addError(__("We can't find a revision to delete."));
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
                return $this->_cmsConfig->canCurrentUserSaveRevision();
            case 'publish':
                return $this->_cmsConfig->canCurrentUserPublishRevision();
            case 'delete':
                return $this->_cmsConfig->canCurrentUserDeleteRevision();
            default:
                return $this->_authorization->isAllowed('Magento_Cms::page');
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
