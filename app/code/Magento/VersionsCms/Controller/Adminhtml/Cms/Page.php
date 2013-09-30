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
 * Cms manage pages controller
 */
class Magento_VersionsCms_Controller_Adminhtml_Cms_Page extends Magento_Adminhtml_Controller_Cms_Page
{
    /**
     * @var array
     */
    protected $_handles = array();

    /**
     * @var Magento_VersionsCms_Model_Config
     */
    protected $_cmsConfig;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_backendAuthSession;

    /**
     * @var Magento_VersionsCms_Model_Page_Version
     */
    protected $_pageVersion;

    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_VersionsCms_Model_Config $cmsConfig
     * @param Magento_Backend_Model_Auth_Session $backendAuthSession
     * @param Magento_VersionsCms_Model_Page_Version $pageVersion
     * @param Magento_Cms_Model_PageFactory $pageFactory
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_VersionsCms_Model_Config $cmsConfig,
        Magento_Backend_Model_Auth_Session $backendAuthSession,
        Magento_VersionsCms_Model_Page_Version $pageVersion,
        Magento_Cms_Model_PageFactory $pageFactory
    ) {
        $this->_cmsConfig = $cmsConfig;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_pageVersion = $pageVersion;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Init actions
     *
     * @return Magento_VersionsCms_Controller_Adminhtml_Cms_Page
     */
    protected function _initAction()
    {
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');

        // add default layout handles for this action
        $this->addActionLayoutHandles();
        $update->addHandle($this->_handles);

        $this->loadLayoutUpdates()
            ->generateLayoutXml()
            ->generateLayoutBlocks();

        $this->_initLayoutMessages('Magento_Adminhtml_Model_Session');

        //load layout, set active menu and breadcrumbs
        $this->_setActiveMenu('Magento_VersionsCms::versionscms_page_page')
            ->_addBreadcrumb(__('CMS'), __('CMS'))
            ->_addBreadcrumb(__('Manage Pages'), __('Manage Pages'));

        $this->_isLayoutLoaded = true;

        return $this;
    }

    /**
     * Prepare and place cms page model into registry
     * with loaded data if id parameter present
     *
     * @return Magento_VersionsCms_Model_Page
     */
    protected function _initPage()
    {
        $this->_title(__('Pages'));

        $pageId = (int)$this->getRequest()->getParam('page_id');
        /** @var Magento_Cms_Model_Page $page */
        $page = $this->_pageFactory->create();

        if ($pageId) {
            $page->load($pageId);
        }

        $this->_coreRegistry->register('cms_page', $page);
        return $page;
    }

    /**
     * Edit CMS page
     */
    public function editAction()
    {
        $page = $this->_initPage();

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $page->setData($data);
        }

        if ($page->getId()){
            if ($page->getUnderVersionControl()) {
                $this->_handles[] = 'adminhtml_cms_page_edit_changes';
            }
        } elseif (!$page->hasUnderVersionControl()) {
            $page->setUnderVersionControl((int)$this->_cmsConfig->getDefaultVersioningStatus());
        }

        $this->_title($page->getId() ? $page->getTitle() : __('New Page'));

        $this->_initAction()->_addBreadcrumb(
            $page->getId() ? __('Edit Page') : __('New Page'),
            $page->getId() ? __('Edit Page') : __('New Page')
        );

        $this->renderLayout();
    }

    /**
     * Action for versions ajax tab
     *
     * @return Magento_VersionsCms_Controller_Adminhtml_Cms_Page_Revision
     */
    public function versionsAction()
    {
        $this->_initPage();

        $this->loadLayout();
        $this->renderLayout();

        return $this;
    }

    /**
     * Mass deletion for versions
     *
     */
    public function massDeleteVersionsAction()
    {
        $ids = $this->getRequest()->getParam('version');
        if (!is_array($ids)) {
            $this->_getSession()->addError(__('Please select version(s).'));
        } else {
            try {
                $userId = $this->_backendAuthSession->getUser()->getId();
                $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();

                foreach ($ids as $id) {
                    $version = $this->_pageVersion->loadWithRestrictions($accessLevel, $userId, $id);

                    if ($version->getId()) {
                        $version->delete();
                    }
                }
                $this->_getSession()->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
                $this->_getSession()->addError(__('Something went wrong while deleting these versions.'));
            }
        }
        $this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'versions'));

        return $this;
    }

    /**
     * Check the permission to run action.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'massDeleteVersions':
                return $this->_cmsConfig->canCurrentUserDeleteVersion();
            default:
                return parent::_isAllowed();
        }
    }
}
