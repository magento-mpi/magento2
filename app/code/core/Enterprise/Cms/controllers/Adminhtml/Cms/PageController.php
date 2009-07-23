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
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

include('app/code/core/Mage/Adminhtml/controllers/Cms/PageController.php');

class Enterprise_Cms_Adminhtml_Cms_PageController extends Mage_Adminhtml_Cms_PageController
{
    protected $_handles = array();

    /**
     * Init actions
     *
     * @return mixed $handle
     * @return Mage_Adminhtml_Cms_PageController
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

        $this->_initLayoutMessages('adminhtml/session');

        //load layout, set active menu and breadcrumbs
        $this->_setActiveMenu('cms/page')
            ->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('cms')->__('Manage Pages'), Mage::helper('cms')->__('Manage Pages'));

        return $this;
    }

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

            $page->setUserId(Mage::getSingleton('admin/session')->getUser()->getId());
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

        if ($page->getId()) {
            $this->_handles[] = 'adminhtml_cms_page_edit_changes';
        }

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
            $model = $this->_initPage();
            $model->setData($data);

            Mage::dispatchEvent('cms_page_prepare_save', array('page' => $model, 'request' => $this->getRequest()));

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('Page was successfully saved'));
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
                $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }


    /**
     * Action for revisions ajax tab
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Page_RevisionController
     */
    public function revisionsAction()
    {
        $this->_initPage();

        $this->loadLayout();
        $this->renderLayout();

        return $this;
    }

    /**
     * Action for versions ajax tab
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Page_RevisionController
     */
    public function versionsAction()
    {
        $this->_initPage();

        $this->loadLayout();
        $this->renderLayout();

        return $this;
    }
}
