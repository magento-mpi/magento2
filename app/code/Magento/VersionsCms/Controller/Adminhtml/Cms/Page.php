<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms;

/**
 * Cms manage pages controller
 */
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
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Core\Filter\Date $dateFilter
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\VersionsCms\Model\Page\Version $pageVersion
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Registry $coreRegistry,
        \Magento\Core\Filter\Date $dateFilter,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\VersionsCms\Model\Page\Version $pageVersion,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->_cmsConfig = $cmsConfig;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_pageVersion = $pageVersion;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context, $coreRegistry, $dateFilter);
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        $update = $this->_view->getLayout()->getUpdate();
        $update->addHandle('default');

        // add default layout handles for this action
        $this->_view->addActionLayoutHandles();
        $update->addHandle($this->_handles);

        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();

        $this->_view->getLayout()->initMessages();

        //load layout, set active menu and breadcrumbs
        $this->_setActiveMenu('Magento_VersionsCms::versionscms_page_page')
            ->_addBreadcrumb(__('CMS'), __('CMS'))
            ->_addBreadcrumb(__('Manage Pages'), __('Manage Pages'));

        $this->_view->setIsLayoutLoaded(true);

        return $this;
    }

    /**
     * Prepare and place cms page model into registry
     * with loaded data if id parameter present
     *
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
     *
     * @return void
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

        $this->_view->renderLayout();
    }

    /**
     * Action for versions ajax tab
     *
     * @return void
     */
    public function versionsAction()
    {
        $this->_initPage();

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Mass deletion for versions
     *
     * @return void
     */
    public function massDeleteVersionsAction()
    {
        $ids = $this->getRequest()->getParam('version');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select version(s).'));
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
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->messageManager->addError(__('Something went wrong while deleting these versions.'));
            }
        }
        $this->_redirect('adminhtml/*/edit', array('_current' => true, 'tab' => 'versions'));
    }

    /**
     * Check the permission to run action.
     *
     * @return bool
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
