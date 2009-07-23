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
     * Retrieve Cms Hierarhy data helper
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
            $this->setFlag(self::FLAG_NO_DISPATCH);
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
     * CMS Page Trees Grid
     *
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Add new tree (forward to edit page)
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Page Tree
     *
     */
    public function editAction()
    {
        $hierarchy  = Mage::getModel('enterprise_cms/hierarchy');
        $node       = Mage::getModel('enterprise_cms/hierarchy_node');
        $treeId     = $this->getRequest()->getParam('tree_id');
        if (is_numeric($treeId)) {
            $hierarchy->load($treeId);
            if ($hierarchy->getId()) {
                $node->loadByHierarchy($hierarchy->getId());
            }
        }
        $hierarchy->setRootNode($node);

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $hierarchy->addData($data);
            $node->addData($data);
        }

        Mage::register('current_hierarchy', $hierarchy);
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
        $redirectUrl    = '*/*';
        $redirectArgs   = array();
        if ($this->getRequest()->isPost()) {
            /* @var $hierarchy Enterprise_Cms_Model_Hierarchy */
            /* @var $node Enterprise_Cms_Model_Hierarchy_Node */
            $hierarchy  = Mage::getModel('enterprise_cms/hierarchy');
            $node       = Mage::getModel('enterprise_cms/hierarchy_node');
            $data       = $this->getRequest()->getPost('cms_hierarchy');
            $hasError   = true;
            if (isset($data['tree_id']) && is_numeric($data['tree_id'])) {
                $hierarchy->load($data['tree_id']);
            } else {
                $data['tree_id'] = null;
            }
            try {
                if (empty($data['page_id'])) {
                    $data['page_id'] = null;
                }
                $hierarchy->addData($data);
                $node->loadByHierarchy($hierarchy->getId());
                $node->addData($data);
                $node->validateHierarchyIdentifier();
                $hierarchy->save();
                // prepare root hierarchy node
                $node->setTreeId($hierarchy->getId());
                $node->setParentNodeId(null);
                $node->setLevel(0);
                $node->setSortOrder(0);
                $node->setRequestUrl($node->getIdentifier());
                $node->save();

                $nodesData = Mage::helper('core')->jsonDecode($data['nodes_data']);
                $node->collectTree($nodesData);

                $hasError = false;
                $this->_getSession()->addSuccess(
                    Mage::helper('enterprise_cms')->__('Page Tree was successfully saved')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('enterprise_cms')->__('Error while saving this Page Tree. Please try again later.')
                );
            }

            if ($hasError) {
                //save data in session
                $this->_getSession()->setFormData($data);
                $redirectUrl = '*/*/edit';
                $redirectArgs['tree_id'] = !empty($data['tree_id']) ? $data['tree_id'] : null;
            } else if (!empty($data['continue_edit'])) {
                $redirectUrl = '*/*/edit';
                $redirectArgs['tree_id'] = $hierarchy->getId();
            }
        }

        $this->_redirect($redirectUrl, $redirectArgs);
    }

    /**
     * Cms Pages Ajax Grid
     *
     */
    public function pageGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->getBlock('cms_page_grid')->toHtml()
        );
    }

    /**
     * Cms Pages Ajax Grid for General
     *
     */
    public function pageGeneralGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->getBlock('cms_page_general_grid')->toHtml()
        );
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
