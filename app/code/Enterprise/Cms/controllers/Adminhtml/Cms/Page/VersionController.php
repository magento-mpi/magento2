<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Manage version controller
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once('Enterprise/Cms/controllers/Adminhtml/Cms/PageController.php');

class Enterprise_Cms_Adminhtml_Cms_Page_VersionController extends Enterprise_Cms_Adminhtml_Cms_PageController
{
    /**
     * Init actions
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Page_VersionController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('Mage_Cms::cms_page')
            ->_addBreadcrumb(Mage::helper('Mage_Cms_Helper_Data')->__('CMS'), Mage::helper('Mage_Cms_Helper_Data')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('Mage_Cms_Helper_Data')->__('Manage Pages'), Mage::helper('Mage_Cms_Helper_Data')->__('Manage Pages'))
        ;
        return $this;
    }

    /**
     * Prepare and place version's model into registry
     * with loaded data if id parameter present
     *
     * @param int $versionId
     * @return Enterprise_Cms_Model_Page_Version
     */
    protected function _initVersion($versionId = null)
    {
        if (is_null($versionId)) {
            $versionId = (int) $this->getRequest()->getParam('version_id');
        }

        $version = Mage::getModel('Enterprise_Cms_Model_Page_Version');
        /* @var $version Enterprise_Cms_Model_Page_Version */

        if ($versionId) {
            $userId = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId();
            $accessLevel = Mage::getSingleton('Enterprise_Cms_Model_Config')->getAllowedAccessLevel();
            $version->loadWithRestrictions($accessLevel, $userId, $versionId);
        }

        Mage::register('cms_page_version', $version);
        return $version;
    }

    /**
     * Edit version of CMS page
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Page_VersionController
     */
    public function editAction()
    {
        $version = $this->_initVersion();

        if (!$version->getId()) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(
                Mage::helper('Enterprise_Cms_Helper_Data')->__('Could not load specified version.'));

            $this->_redirect('*/cms_page/edit',
                array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }

        $page = $this->_initPage();

        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getFormData(true);
        if (!empty($data)) {
            $_data = $version->getData();
            $_data = array_merge($_data, $data);
            $version->setData($_data);
        }

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('Enterprise_Cms_Helper_Data')->__('Edit Version'),
                Mage::helper('Enterprise_Cms_Helper_Data')->__('Edit Version'));

        $this->renderLayout();

        return $this;
    }

    /**
     * Save Action
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Page_VersionController
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            // init model and set data
            $version = $this->_initVersion();

            // if current user not publisher he can't change owner
            if (!Mage::getSingleton('Enterprise_Cms_Model_Config')->canCurrentUserPublishRevision()) {
                unset($data['user_id']);
            }
            $version->addData($data);

            // try to save it
            try {
                // save the data
                $version->save();

                // display success message
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_Cms_Helper_Data')->__('The version has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->setFormData(false);
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

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->setFormData($data);
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
     * @return Enterprise_Cms_Adminhtml_Cms_Page_VersionController
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
     * @return Enterprise_Cms_Adminhtml_Cms_Page_VersionController
     */
    public function massDeleteRevisionsAction()
    {
        $ids = $this->getRequest()->getParam('revision');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select revision(s).'));
        }
        else {
            try {
                $userId = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId();
                $accessLevel = Mage::getSingleton('Enterprise_Cms_Model_Config')->getAllowedAccessLevel();

                foreach ($ids as $id) {
                    $revision = Mage::getSingleton('Enterprise_Cms_Model_Page_Revision')
                        ->loadWithRestrictions($accessLevel, $userId, $id);

                    if ($revision->getId()) {
                        $revision->delete();
                    }
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(Mage::helper('Enterprise_Cms_Helper_Data')->__('An error occurred while deleting revisions.'));
            }
        }
        $this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'revisions'));

        return $this;
    }

    /**
     * Delete action
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Page_VersionController
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
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_Cms_Helper_Data')->__('The version has been deleted.'));
                $this->_redirect('*/cms_page/edit', array('page_id' => $version->getPageId()));
                return;
            } catch (Mage_Core_Exception $e) {
                // display error message
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
                $error = true;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_Cms_Helper_Data')->__('An error occurred while deleting the version.'));
                $error = true;
            }

            // go back to edit form
            if ($error) {
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }
        // display error message
        Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Enterprise_Cms_Helper_Data')->__('Unable to find a version to delete.'));
        // go to grid
        $this->_redirect('*/cms_page/edit', array('_current' => true));
        return $this;
    }

    /**
     * New Version
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Page_VersionController
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
                $version->setUserId(Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId());
            }

            if (isset($data['revision_id'])) {
                $data = $this->_filterPostData($data);
                $version->setInitialRevisionData($data);
            }

            // try to save it
            try {
                $version->save();
                // display success message
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Enterprise_Cms_Helper_Data')->__('The new version has been created.'));
                // clear previously saved data from session
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->setFormData(false);
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
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
                if ($this->_getRefererUrl()) {
                    // save data in session
                    Mage::getSingleton('Mage_Adminhtml_Model_Session')->setFormData($data);
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
                return Mage::getSingleton('Enterprise_Cms_Model_Config')->canCurrentUserSaveVersion();
                break;
            case 'delete':
                return Mage::getSingleton('Enterprise_Cms_Model_Config')->canCurrentUserDeleteVersion();
                break;
            case 'massDeleteRevisions':
                return Mage::getSingleton('Enterprise_Cms_Model_Config')->canCurrentUserDeleteRevision();
                break;
            default:
                return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Cms::page');
                break;
        }
    }
}