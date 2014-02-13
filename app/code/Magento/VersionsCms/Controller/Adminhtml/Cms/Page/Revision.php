<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

/**
 * Manage revision controller
 */
class Revision
    extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_cmsPage;

    /**
     * @var \Magento\Core\Model\Design
     */
    protected $_design;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Core\Filter\Date $dateFilter
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\VersionsCms\Model\Page\Version $pageVersion
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Cms\Model\Page $cmsPage
     * @param \Magento\Core\Model\Design $design
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Registry $coreRegistry,
        \Magento\Core\Filter\Date $dateFilter,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\VersionsCms\Model\Page\Version $pageVersion,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\Page $cmsPage,
        \Magento\Core\Model\Design $design,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_cmsPage = $cmsPage;
        $this->_design = $design;
        parent::__construct(
            $context,
            $coreRegistry,
            $dateFilter,
            $cmsConfig,
            $backendAuthSession,
            $pageVersion,
            $pageFactory
        );
    }


    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Cms::cms_page')
            ->_addBreadcrumb(__('CMS'), __('CMS'))
            ->_addBreadcrumb(__('Manage Pages'), __('Manage Pages'));
        return $this;
    }

    /**
     * Prepare and place revision model into registry
     * with loaded data if id parameter present
     *
     * @param int $revisionId
     * @return \Magento\VersionsCms\Model\Page\Revision
     */
    protected function _initRevision($revisionId = null)
    {
        if (is_null($revisionId)) {
            $revisionId = (int)$this->getRequest()->getParam('revision_id');
        }

        $revision = $this->_objectManager->create('Magento\VersionsCms\Model\Page\Revision');
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
     *
     * @return void
     */
    public function editAction()
    {
        $revisionId = $this->getRequest()->getParam('revision_id');
        $revision = $this->_initRevision($revisionId);

        if ($revisionId && !$revision->getId()) {
            $this->messageManager->addError(__('We could not load the specified revision.'));

            $this->_redirect('adminhtml/cms_page/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $_data = $revision->getData();
            $_data = array_merge($_data, $data);
            $revision->setData($_data);
        }

        $this->_initAction()->_addBreadcrumb(__('Edit Revision'), __('Edit Revision'));
        $this->_view->renderLayout();
    }

    /**
     * Save action
     *
     * @return void
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
                $this->_redirect('adminhtml/*/' . $this->getRequest()->getParam('back'), array(
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
                $this->messageManager->addSuccess(__('You have saved the revision.'));
                // clear previously saved data from session
                $this->_session->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('adminhtml/*/' . $this->getRequest()->getParam('back'), array(
                        'page_id' => $revision->getPageId(),
                        'revision_id' => $revision->getId()
                    ));
                    return;
                }
                // go to grid
                $this->_redirect('adminhtml/cms_page_version/edit', array(
                    'page_id' => $revision->getPageId(),
                    'version_id' => $revision->getVersionId()
                ));
                return;
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_session->setFormData($data);
                // redirect to edit form
                $this->_redirect('adminhtml/*/edit', array(
                    'page_id' => $this->getRequest()->getParam('page_id'),
                    'revision_id' => $this->getRequest()->getParam('revision_id'),
                ));
                return;
            }
        }
    }

    /**
     * Publishing revision
     *
     * @return void
     */
    public function publishAction()
    {
        $revision = $this->_initRevision();

        try {
            $revision->publish();
            // display success message
            $this->messageManager->addSuccess(__('You have published the revision.'));
            $this->_redirect('adminhtml/cms_page/edit', array('page_id' => $revision->getPageId()));
            return;
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
            // redirect to edit form
            $this->_redirect('adminhtml/*/edit', array(
                'page_id' => $this->getRequest()->getParam('page_id'),
                'revision_id' => $this->getRequest()->getParam('revision_id')
            ));
            return;
        }
    }

    /**
     * Prepares page with iframe
     *
     * @return void
     */
    public function previewAction()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if (empty($data) || !isset($data['page_id'])) {
            $this->_forward('noroute');
            return ;
        }

        $page = $this->_initPage();
        $this->_view->loadLayout();

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
            $this->_view->getLayout()->getBlock('store_switcher')->setStoreIds($stores);
        }

        // Setting default values for selected store and revision
        $data['preview_selected_store'] = 0;
        $data['preview_selected_revision'] = 0;

        $this->_view->getLayout()->getBlock('preview_form')->setFormData($data);

        // Remove revision switcher if page is out of version control
        if (!$page->getUnderVersionControl()) {
            $this->_view->getLayout()->unsetChild('tools', 'revision_switcher');
        }

        $this->_view->renderLayout();
    }

    /**
     * Generates preview of page
     *
     * @return void
     */
    public function dropAction()
    {
        $this->_objectManager->get('Magento\Translate\InlineInterface')->disable();
        $this->_objectManager->get('Magento\App\State')
            ->emulateAreaCode('frontend', array($this, 'previewFrontendPage'));
    }

    /**
     * Generates preview of page. Assumed to be run in frontend area
     *
     * @return void
     */
    public function previewFrontendPage()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if (!empty($data) && isset($data['page_id'])) {
            // init model and set data
            $page = $this->_cmsPage->load($data['page_id']);
            if (!$page->getId()) {
                $this->_forward('noroute');
                return ;
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
            $this->_localeResolver->emulate($selectedStoreId);
            $this->_storeManager->setCurrentStore($this->_storeManager->getStore($selectedStoreId));

            $theme = $this->_objectManager->get('Magento\View\DesignInterface')
                ->getConfigurationDesignTheme(null, array('store' => $selectedStoreId));
            $this->_objectManager->get('Magento\View\DesignInterface')->setDesignTheme($theme, 'frontend');

            $designChange = $this->_design->loadChange($selectedStoreId);

            if ($designChange->getData()) {
                $this->_objectManager->get('Magento\View\DesignInterface')
                    ->setDesignTheme($designChange->getDesign());
            }

            // add handles used to render cms page on frontend
            $this->_view->getLayout()->getUpdate()->addHandle('default');
            $this->_view->getLayout()->getUpdate()->addHandle('cms_page_view');
            $this->_objectManager->get('Magento\Cms\Helper\Page')->renderPageExtended($this);
            $this->_localeResolver->revert();

        } else {
            $this->_forward('noroute');
        }
    }

    /**
     * Delete action
     *
     * @return void
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
                $this->messageManager->addSuccess(__('You have deleted the revision.'));
                $this->_redirect('adminhtml/cms_page_version/edit', array(
                        'page_id' => $revision->getPageId(),
                        'version_id' => $revision->getVersionId()
                    ));
                return;
            } catch (\Magento\Core\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->messageManager->addError(__('Something went wrong while deleting the revision.'));
                $error = true;
            }

            // go back to edit form
            if ($error) {
                $this->_redirect('adminhtml/*/edit', array('_current' => true));
                return;
            }
        }
        // display error message
        $this->messageManager->addError(__("We can't find a revision to delete."));
        // go to grid
        $this->_redirect('adminhtml/cms_page/edit', array('_current' => true));
    }

    /**
     * Check the permission to run it
     *
     * @return bool
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
     * New Revision action
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
}
