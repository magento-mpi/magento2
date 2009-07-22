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
 * Manage revision controller
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

include('app/code/core/Enterprise/Cms/controllers/Adminhtml/Cms/PageController.php');

class Enterprise_Cms_Adminhtml_Cms_Page_RevisionController extends Enterprise_Cms_Adminhtml_Cms_PageController
{
    /**
     * Edit revision of CMS page
     */
    public function editAction()
    {
        $page = $this->_initPage();

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $page->setData($data);
        }

        $page->setHideNotRevisionedAttributes(true);

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

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('enterprise_cms')->__('Revision was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit',
                        array(
                            'page_id' => $model->getId(),
                            'revision_id' => $model->getRevisionId()
                        ));
                    return;
                }
                // go to grid
                $this->_redirect('*/cms_page/edit', array('page_id' => $model->getId()));
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit',
                    array(
                        'page_id' => $this->getRequest()->getParam('page_id'),
                        'page_id' => $this->getRequest()->getParam('revision_id'),
                        ));
                return;
            }
        }
        $this->_redirect('*/*/');
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

    /**
     * Action for versions ajax tab
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Page_RevisionController
     */
    public function versionsgridAction()
    {
        $this->_initPage();

        $this->loadLayout();
        $this->renderLayout();

        return $this;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'edit':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('cms/page/save_revision');
                break;
            case 'publish':
                return Mage::getSingleton('admin/session')->isAllowed('cms/page/publish_revision');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('cms/page/delete_revision');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('cms/page');
                break;
        }
    }
}
