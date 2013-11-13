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
namespace Magento\VersionsCms\Controller\Adminhtml\Cms;

class Page extends \Magento\Cms\Controller\Adminhtml\Page
{
    /**
     * @var array
     */
    protected $_handles = array();

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\VersionsCms\Model\Page\Version
     */
    protected $_pageVersion;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\App\Action\Title
     */
    protected $_title;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\VersionsCms\Model\Page\Version $pageVersion
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\App\Action\Title $title
     * @param \Magento\Core\Filter\Date $dateFilter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\VersionsCms\Model\Page\Version $pageVersion,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\App\Action\Title $title,
        \Magento\Core\Filter\Date $dateFilter
    ) {
        $this->_cmsConfig = $cmsConfig;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_pageVersion = $pageVersion;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context, $coreRegistry, $title, $dateFilter);
        $this->_title = $title;
    }

    /**
     * Init actions
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page
     */
    protected function _initAction()
    {
        $update = $this->_layoutServices->getLayout()->getUpdate();
        $update->addHandle('default');

        // add default layout handles for this action
        $this->addActionLayoutHandles();
        $update->addHandle($this->_handles);

        $this->loadLayoutUpdates()
            ->generateLayoutXml()
            ->generateLayoutBlocks();

        $this->_layoutServices->getLayout()->initMessages('Magento\Adminhtml\Model\Session');

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
     * @param string $idFieldName
     * @return \Magento\Cms\Model\Page
     */
    protected function _initPage()
    {
        $this->_title->add(__('Pages'));

        $pageId = (int)$this->getRequest()->getParam('page_id');
        /** @var \Magento\Cms\Model\Page $page */
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

        $this->_title->add($page->getId() ? $page->getTitle() : __('New Page'));

        $this->_initAction()->_addBreadcrumb(
            $page->getId() ? __('Edit Page') : __('New Page'),
            $page->getId() ? __('Edit Page') : __('New Page')
        );

        $this->_layoutServices->renderLayout();
    }

    /**
     * Action for versions ajax tab
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision
     */
    public function versionsAction()
    {
        $this->_initPage();

        $this->_layoutServices->loadLayout();
        $this->_layoutServices->renderLayout();

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
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->_getSession()->addError(__('Something went wrong while deleting these versions.'));
            }
        }
        $this->_redirect('adminhtml/*/edit', array('_current' => true, 'tab' => 'versions'));

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
