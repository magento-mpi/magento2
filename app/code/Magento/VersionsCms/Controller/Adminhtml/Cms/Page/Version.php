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
 * Manage version controller
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

class Version
    extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page
{
    /**
     * @var \Magento\VersionsCms\Model\Page\VersionFactory
     */
    protected $_pageVersionFactory;


    /**
     * @var \Magento\VersionsCms\Model\Page\Revision
     */
    protected $_pageRevision;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Core\Filter\Date $dateFilter
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\VersionsCms\Model\Page\Version $pageVersion
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\VersionsCms\Model\Page\VersionFactory $pageVersionFactory
     * @param \Magento\VersionsCms\Model\Page\Revision $pageRevision
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Registry $coreRegistry,
        \Magento\Core\Filter\Date $dateFilter,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\VersionsCms\Model\Page\Version $pageVersion,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\VersionsCms\Model\Page\VersionFactory $pageVersionFactory,
        \Magento\VersionsCms\Model\Page\Revision $pageRevision
    ) {
        $this->_pageVersionFactory = $pageVersionFactory;
        $this->_pageRevision = $pageRevision;
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
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
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
     * Prepare and place version's model into registry
     * with loaded data if id parameter present
     *
     * @param int $versionId
     * @return \Magento\VersionsCms\Model\Page\Version
     */
    protected function _initVersion($versionId = null)
    {
        if (is_null($versionId)) {
            $versionId = (int)$this->getRequest()->getParam('version_id');
        }

        $version = $this->_pageVersionFactory->create();
        /* @var $version \Magento\VersionsCms\Model\Page\Version */

        if ($versionId) {
            $userId = $this->_backendAuthSession->getUser()->getId();
            $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();
            $version->loadWithRestrictions($accessLevel, $userId, $versionId);
        }

        $this->_coreRegistry->register('cms_page_version', $version);
        return $version;
    }

    /**
     * Edit version of CMS page
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
     */
    public function editAction()
    {
        $version = $this->_initVersion();

        if (!$version->getId()) {
            $this->messageManager->addError(__('We could not load the specified revision.'));
            $this->_redirect('adminhtml/cms_page/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }

        $this->_initPage();

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $_data = $version->getData();
            $_data = array_merge($_data, $data);
            $version->setData($_data);
        }

        $this->_initAction()
            ->_addBreadcrumb(__('Edit Version'),
                __('Edit Version'));

        $this->_view->renderLayout();
    }

    /**
     * Save Action
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
     */
    public function saveAction()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            // init model and set data
            $version = $this->_initVersion();

            // if current user not publisher he can't change owner
            if (!$this->_cmsConfig->canCurrentUserPublishRevision()) {
                unset($data['user_id']);
            }
            $version->addData($data);

            // try to save it
            try {
                // save the data
                $version->save();

                // display success message
                $this->messageManager->addSuccess(__('You have saved the version.'));
                // clear previously saved data from session
                $this->_session->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('adminhtml/*/' . $this->getRequest()->getParam('back'),
                        array(
                            'page_id' => $version->getPageId(),
                            'version_id' => $version->getId()
                        ));
                    return;
                }
                // go to grid
                $this->_redirect('adminhtml/cms_page/edit', array('page_id' => $version->getPageId()));
                return;

            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_session->setFormData($data);
                // redirect to edit form
                $this->_redirect('adminhtml/*/edit', array(
                    'page_id' => $this->getRequest()->getParam('page_id'),
                    'version_id' => $this->getRequest()->getParam('version_id'),
                ));
                return;
            }
        }
    }

    /**
     * Action for ajax grid with revisions
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
     */
    public function revisionsAction()
    {
        $this->_initVersion();
        $this->_initPage();

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Mass deletion for revisions
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
     */
    public function massDeleteRevisionsAction()
    {
        $ids = $this->getRequest()->getParam('revision');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select revision(s).'));
        } else {
            try {
                $userId = $this->_backendAuthSession->getUser()->getId();
                $accessLevel = $this->_cmsConfig->getAllowedAccessLevel();

                foreach ($ids as $id) {
                    $revision = $this->_pageRevision->loadWithRestrictions($accessLevel, $userId, $id);

                    if ($revision->getId()) {
                        $revision->delete();
                    }
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->messageManager->addError(__('Something went wrong while deleting the revisions.'));
            }
        }
        $this->_redirect('adminhtml/*/edit', array('_current' => true, 'tab' => 'revisions'));
    }

    /**
     * Delete action
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('version_id');
        if ($id) {
             // init model
            $version = $this->_initVersion();
            $error = false;
            try {
                $version->delete();
                // display success message
                $this->messageManager->addSuccess(__('You have deleted the version.'));
                $this->_redirect('adminhtml/cms_page/edit', array('page_id' => $version->getPageId()));
                return;
            } catch (\Magento\Core\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->messageManager->addError(__('Something went wrong while deleting this version.'));
                $error = true;
            }

            // go back to edit form
            if ($error) {
                $this->_redirect('adminhtml/*/edit', array('_current' => true));
                return;
            }
        }
        // display error message
        $this->messageManager->addError(__("We can't find a version to delete."));
        // go to grid
        $this->_redirect('adminhtml/cms_page/edit', array('_current' => true));
    }

    /**
     * New Version
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
     */
    public function newAction()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            // init model and set data
            $version = $this->_initVersion();

            $version->addData($data)->unsetData($version->getIdFieldName());

            // only if user not specified we set current user as owner
            if (!$version->getUserId()) {
                $version->setUserId($this->_backendAuthSession->getUser()->getId());
            }

            if (isset($data['revision_id'])) {
                $data = $this->_filterPostData($data);
                $version->setInitialRevisionData($data);
            }

            // try to save it
            try {
                $version->save();
                // display success message
                $this->messageManager->addSuccess(__('You have created the new version.'));
                // clear previously saved data from session
                $this->_session->setFormData(false);
                if (isset($data['revision_id'])) {
                    $this->_redirect('adminhtml/cms_page_revision/edit', array(
                        'page_id' => $version->getPageId(),
                        'revision_id' => $version->getLastRevision()->getId()
                    ));
                } else {
                    $this->_redirect('adminhtml/cms_page_version/edit', array(
                        'page_id' => $version->getPageId(),
                        'version_id' => $version->getId()
                    ));
                }
                return;
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                if ($this->_redirect->getRefererUrl()) {
                    // save data in session
                    $this->_session->setFormData($data);
                }
                // redirect to edit form
                $editUrl = $this->getUrl('adminhtml/cms_page/edit',
                    array('page_id' => $this->getRequest()->getParam('page_id')));
                $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($editUrl));
                return;
            }
        }
    }

    /**
     * Check the permission to run it
     * May be in future there will be separate permissions for operations with version
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return $this->_cmsConfig->canCurrentUserSaveVersion();
            case 'delete':
                return $this->_cmsConfig->canCurrentUserDeleteVersion();
            case 'massDeleteRevisions':
                return $this->_cmsConfig->canCurrentUserDeleteRevision();
            default:
                return $this->_authorization->isAllowed('Magento_Cms::page');
        }
    }
}
