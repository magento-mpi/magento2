<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms manage pages controller
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

include('app/code/core/Mage/Adminhtml/controllers/Cms/PageController.php');

class Enterprise_Cms_Adminhtml_Cms_PageController extends Mage_Adminhtml_Cms_PageController
{

    /**
     * Prepare ans place cms page model into registry
     * with loaded data if id parameter present
     *
     * @param string $idFieldName
     * @return Mage_Cms_Model_Page
     */
    protected function _initPage()
    {
        $pageId = (int) $this->getRequest()->getParam('page_id');
        $revisionId = (int) $this->getRequest()->getParam('revision_id');

        $page = Mage::getModel('enterprise_cms/page');

        if ($pageId) {
            if ($revisionId) {
                $page->setRevisionId($revisionId);
            }

            $page->setAccessLevel(Mage::getSingleton('enterprise_cms/config')->getAllowedAccessLevel());

            $page->load($pageId);

            /**
             * @todo Need to throw exception about - can't load page
             */
        }

        Mage::register('cms_page', $page);
        return $page;
    }

    /**
     * Edit revision of CMS page
     */
    public function editAction()
    {
        $page = $this->_initPage();

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $page->setData($data);
        }

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb($page->getId() ? Mage::helper('cms')->__('Edit Page') : Mage::helper('cms')->__('New Page'), $page->getId() ? Mage::helper('cms')->__('Edit Page') : Mage::helper('cms')->__('New Page'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            // init model and set data
            $page = $this->_initPage();
            $page->setData($data);

            if (Mage::getSingleton('enterprise_cms/config')->isCurrentUserCanPublish()) {
                Mage::dispatchEvent('cms_page_prepare_save', array('page' => $page, 'request' => $this->getRequest()));
            }

            // try to save it
            try {
                // save the data
                $page->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('enterprise_cms')->__('Revision was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('page_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('page_id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }


    /**
     * Action for revisions ajax tab
     *
     * @return unknown_type
     */
    public function revisionsAction()
    {
        $this->_initPage();

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Action for versions ajax tab
     *
     */
    public function versionsAction()
    {
        $this->_initPage();

        $this->loadLayout();
        $this->renderLayout();
    }

    public function publishRevisionAction()
    {

    }

    public function deleteRevisionAction()
    {

    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
                return Mage::getSingleton('admin/session')->isAllowed('cms/page/new');
            case 'publishRevision':
                return Mage::getSingleton('admin/session')->isAllowed('cms/page/publish_revision');
            case 'deleteRevision':
                return Mage::getSingleton('admin/session')->isAllowed('cms/page/delete_revision');
            default:
                return parent::_isAllowed();
                break;
        }
    }
}
