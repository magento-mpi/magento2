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
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\VersionsCms\Controller\Adminhtml\Cms;

class Page extends \Magento\Adminhtml\Controller\Cms\Page
{
    protected $_handles = array();

    /**
     * Init actions
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page
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

        $this->_initLayoutMessages('Magento\Adminhtml\Model\Session');

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
     * @return Magento_VersionsCms_Model_Page
     */
    protected function _initPage()
    {
        $this->_title(__('Pages'));

        $pageId = (int) $this->getRequest()->getParam('page_id');
        $page = \Mage::getModel('Magento\Cms\Model\Page');

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

        $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getFormData(true);
        if (! empty($data)) {
            $page->setData($data);
        }

        if ($page->getId()){
            if ($page->getUnderVersionControl()) {
                $this->_handles[] = 'adminhtml_cms_page_edit_changes';
            }
        } else if (!$page->hasUnderVersionControl()) {
            $page->setUnderVersionControl((int)\Mage::getSingleton('Magento\VersionsCms\Model\Config')->getDefaultVersioningStatus());
        }

        $this->_title($page->getId() ? $page->getTitle() : __('New Page'));

        $this->_initAction()
            ->_addBreadcrumb($page->getId() ? __('Edit Page')
                    : __('New Page'),
                $page->getId() ? __('Edit Page')
                    : __('New Page'));

        $this->renderLayout();
    }

    /**
     * Action for versions ajax tab
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision
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
        }
        else {
            try {
                $userId = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->getId();
                $accessLevel = \Mage::getSingleton('Magento\VersionsCms\Model\Config')->getAllowedAccessLevel();

                foreach ($ids as $id) {
                    $version = \Mage::getSingleton('Magento\VersionsCms\Model\Page\Version')
                        ->loadWithRestrictions($accessLevel, $userId, $id);

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
                \Mage::logException($e);
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
                return \Mage::getSingleton('Magento\VersionsCms\Model\Config')->canCurrentUserDeleteVersion();
                break;
            default:
                return parent::_isAllowed();
                break;
        }
    }
}
