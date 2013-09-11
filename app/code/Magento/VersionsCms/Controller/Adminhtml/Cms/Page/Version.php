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
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

class Version extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Page
{
    /**
     * Init actions
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
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
     * Prepare and place version's model into registry
     * with loaded data if id parameter present
     *
     * @param int $versionId
     * @return \Magento\VersionsCms\Model\Page\Version
     */
    protected function _initVersion($versionId = null)
    {
        if (is_null($versionId)) {
            $versionId = (int) $this->getRequest()->getParam('version_id');
        }

        $version = \Mage::getModel('Magento\VersionsCms\Model\Page\Version');
        /* @var $version \Magento\VersionsCms\Model\Page\Version */

        if ($versionId) {
            $userId = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->getId();
            $accessLevel = \Mage::getSingleton('Magento\VersionsCms\Model\Config')->getAllowedAccessLevel();
            $version->loadWithRestrictions($accessLevel, $userId, $versionId);
        }

        \Mage::register('cms_page_version', $version);
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
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
                __('We could not load the specified revision.'));

            $this->_redirect('*/cms_page/edit',
                array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }

        $page = $this->_initPage();

        $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $_data = $version->getData();
            $_data = array_merge($_data, $data);
            $version->setData($_data);
        }

        $this->_initAction()
            ->_addBreadcrumb(__('Edit Version'),
                __('Edit Version'));

        $this->renderLayout();

        return $this;
    }

    /**
     * Save Action
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            // init model and set data
            $version = $this->_initVersion();

            // if current user not publisher he can't change owner
            if (!\Mage::getSingleton('Magento\VersionsCms\Model\Config')->canCurrentUserPublishRevision()) {
                unset($data['user_id']);
            }
            $version->addData($data);

            // try to save it
            try {
                // save the data
                $version->save();

                // display success message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You have saved the version.'));
                // clear previously saved data from session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/' . $this->getRequest()->getParam('back'),
                        array(
                            'page_id' => $version->getPageId(),
                            'version_id' => $version->getId()
                        ));
                    return;
                }
                // go to grid
                $this->_redirect('*/cms_page/edit', array('page_id' => $version->getPageId()));
                return;

            } catch (\Exception $e) {
                // display error message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                // save data in session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit',
                    array(
                        'page_id' => $this->getRequest()->getParam('page_id'),
                        'version_id' => $this->getRequest()->getParam('version_id'),
                        ));
                return;
            }
        }
        return $this;
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

        $this->loadLayout();
        $this->renderLayout();

        return $this;
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
            $this->_getSession()->addError(__('Please select revision(s).'));
        }
        else {
            try {
                $userId = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->getId();
                $accessLevel = \Mage::getSingleton('Magento\VersionsCms\Model\Config')->getAllowedAccessLevel();

                foreach ($ids as $id) {
                    $revision = \Mage::getSingleton('Magento\VersionsCms\Model\Page\Revision')
                        ->loadWithRestrictions($accessLevel, $userId, $id);

                    if ($revision->getId()) {
                        $revision->delete();
                    }
                }
                $this->_getSession()->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                \Mage::logException($e);
                $this->_getSession()->addError(__('Something went wrong while deleting the revisions.'));
            }
        }
        $this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'revisions'));

        return $this;
    }

    /**
     * Delete action
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('version_id')) {
             // init model
            $version = $this->_initVersion();
            $error = false;
            try {
                $version->delete();
                // display success message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You have deleted the version.'));
                $this->_redirect('*/cms_page/edit', array('page_id' => $version->getPageId()));
                return;
            } catch (\Magento\Core\Exception $e) {
                // display error message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                \Mage::logException($e);
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('Something went wrong while deleting this version.'));
                $error = true;
            }

            // go back to edit form
            if ($error) {
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }
        // display error message
        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__("We can't find a version to delete."));
        // go to grid
        $this->_redirect('*/cms_page/edit', array('_current' => true));
        return $this;
    }

    /**
     * New Version
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Version
     */
    public function newAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            // init model and set data
            $version = $this->_initVersion();

            $version->addData($data)
                ->unsetData($version->getIdFieldName());

            // only if user not specified we set current user as owner
            if (!$version->getUserId()) {
                $version->setUserId(\Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->getId());
            }

            if (isset($data['revision_id'])) {
                $data = $this->_filterPostData($data);
                $version->setInitialRevisionData($data);
            }

            // try to save it
            try {
                $version->save();
                // display success message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You have created the new version.'));
                // clear previously saved data from session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData(false);
                if (isset($data['revision_id'])) {
                    $this->_redirect('*/cms_page_revision/edit', array(
                        'page_id' => $version->getPageId(),
                        'revision_id' => $version->getLastRevision()->getId()
                    ));
                } else {
                    $this->_redirect('*/cms_page_version/edit', array(
                        'page_id' => $version->getPageId(),
                        'version_id' => $version->getId()
                    ));
                }
                return;
            } catch (\Exception $e) {
                // display error message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                if ($this->_getRefererUrl()) {
                    // save data in session
                    \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData($data);
                }
                // redirect to edit form
                $this->_redirectReferer($this->getUrl('*/cms_page/edit',
                    array('page_id' => $this->getRequest()->getParam('page_id'))));
                return;
            }
        }
        return $this;
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
                return \Mage::getSingleton('Magento\VersionsCms\Model\Config')->canCurrentUserSaveVersion();
                break;
            case 'delete':
                return \Mage::getSingleton('Magento\VersionsCms\Model\Config')->canCurrentUserDeleteVersion();
                break;
            case 'massDeleteRevisions':
                return \Mage::getSingleton('Magento\VersionsCms\Model\Config')->canCurrentUserDeleteRevision();
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Cms::page');
                break;
        }
    }
}
