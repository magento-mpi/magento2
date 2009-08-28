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
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Admihtml Manage Cms Hierarchy Controller
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Adminhtml_Cms_HierarchyController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Retrieve Cms Hierarchy data helper
     *
     * @return Enterprise_Cms_Helper_Hierarchy
     */
    protected function _getHelper()
    {
        return Mage::helper('enterprise_cms/hierarchy');
    }

    /**
     * Controller pre dispatch method
     *
     * @return Enterprise_Cms_HierarchyController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_getHelper()->isEnabled()) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return Enterprise_Cms_HierarchyController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/hierarchy')
            ->_addBreadcrumb(Mage::helper('enterprise_cms')->__('CMS'),
                Mage::helper('enterprise_cms')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('enterprise_cms')->__('CMS Page Trees'),
                Mage::helper('enterprise_cms')->__('CMS Page Trees'));
        return $this;
    }

    /**
     * Edit Page Tree
     *
     */
    public function indexAction()
    {
        $node = Mage::getModel('enterprise_cms/hierarchy_node');

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $node->addData($data);
        }

        Mage::register('current_hierarchy_node', $node);

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Save changes
     *
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            /** @var $node Enterprise_Cms_Model_Hierarchy_Node */
            $node       = Mage::getModel('enterprise_cms/hierarchy_node');
            $data       = $this->getRequest()->getPost();
            $hasError   = true;

            try {
                if (!empty($data['nodes_data'])) {
                    $nodesData = Mage::helper('core')->jsonDecode($data['nodes_data']);
                } else {
                    $nodesData = array();
                }
                if (!empty($data['removed_nodes'])) {
                    $removedNodes = explode(',', $data['removed_nodes']);
                } else {
                    $removedNodes = array();
                }

                $node->collectTree($nodesData, $removedNodes);

                $hasError = false;
                $this->_getSession()->addSuccess(
                    Mage::helper('enterprise_cms')->__('Hierarchy was successfully saved')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    $e->getMessage() . Mage::helper('enterprise_cms')->__('Error while saving this Hierarchy. Please try again later.')
                );
            }

            if ($hasError) {
                //save data in session
                $this->_getSession()->setFormData($data);
            }
        }

        $this->_redirect('*/*/');
    }

    /**
     * Cms Pages Ajax Grid
     *
     */
    public function pageGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/hierarchy');
    }
}
