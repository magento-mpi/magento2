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
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
     * Current Scope
     *
     * @var string
     */
    protected $_scope = Enterprise_Cms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT;

    /**
     * Current ScopeId
     *
     * @var int
     */
    protected $_scopeId = Enterprise_Cms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT_ID;

    /**
     * Current Website
     *
     * @var string
     */
    protected $_website = '';

    /**
     * Current Store
     *
     * @var string
     */
    protected $_store = '';

    /**
     * Controller pre dispatch method
     *
     * @return Enterprise_Cms_HierarchyController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::helper('enterprise_cms/hierarchy')->isEnabled()) {
            if ($this->getRequest()->getActionName() != 'noroute') {
                $this->_forward('noroute');
            }
        }
        return $this;
    }

    /**
     * Init scope and scope code by website and store for actions
     *
     * @return null
     */
    protected function _initScope()
    {
        $this->_website = $this->getRequest()->getParam('website');
        $this->_store   = $this->getRequest()->getParam('store');

        if (!is_null($this->_website)) {
            $this->_scope = Enterprise_Cms_Model_Hierarchy_Node::NODE_SCOPE_WEBSITE;
            $website = Mage::app()->getWebsite($this->_website);
            $this->_scopeId = $website->getId();
            $this->_website = $website->getCode();
        }

        if (!is_null($this->_store)) {
            $this->_scope = Enterprise_Cms_Model_Hierarchy_Node::NODE_SCOPE_STORE;
            $store = Mage::app()->getStore($this->_store);
            $this->_scopeId = $store->getId();
            $this->_store = $store;
        }
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
        $this->_title($this->__('CMS'))
             ->_title($this->__('Pages'))
             ->_title($this->__('Manage Hierarchy'));

        $this->_getLockModel()->revalidate();

        if ($this->_getLockModel()->isLockedByMe()) {
            $this->_getSession()->addNotice(
                Mage::helper('enterprise_cms')->__('This Page is locked by you.')
            );
        }

        if ($this->_getLockModel()->isLockedByOther()) {
            $this->_getSession()->addNotice(
                Mage::helper('enterprise_cms')->__("This Page is locked by '%s'.", $this->_getLockModel()->getUserName())
            );
        }

        $this->_initScope();

        $nodeModel = Mage::getModel('enterprise_cms/hierarchy_node',
                array('scope' => $this->_scope, 'scope_id' => $this->_scopeId));

        // restore data if exists
        $formData = $this->_getSession()->getFormData(true);
        if (!empty($formData)) {
            $nodeModel->addData($formData);
            unset($formData);
        }

        Mage::register('current_hierarchy_node', $nodeModel);

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Copy hierarchy from one scope to other scopes
     *
     * @return null
     */
    public function copyAction()
    {
        $scopes = $this->getRequest()->getParam('scopes');
        if ($this->getRequest()->isPost() && is_array($scopes) && !empty($scopes)) {
            $this->_initScope();
            /** @var $nodeModel Enterprise_Cms_Model_Hierarchy_Node */
            $nodeModel = Mage::getModel('enterprise_cms/hierarchy_node', array(
                'scope' =>  $this->_scope,
                'scope_id' => $this->_scopeId,
            ));
            $nodeHeritageModel = $nodeModel->getHeritage();
            try {
                foreach (array_unique($scopes) as $value) {
                    $scopeId = false;
                    $scope = Enterprise_Cms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT;
                    if (0 === strpos($value, Enterprise_Cms_Helper_Hierarchy::SCOPE_PREFIX_WEBSITE)) {
                        $scopeId = (int)str_replace(Enterprise_Cms_Helper_Hierarchy::SCOPE_PREFIX_WEBSITE, '', $value);
                        $scope = Enterprise_Cms_Model_Hierarchy_Node::NODE_SCOPE_WEBSITE;
                    } elseif (0 === strpos($value, Enterprise_Cms_Helper_Hierarchy::SCOPE_PREFIX_STORE)) {
                        $scopeId = (int)str_replace(Enterprise_Cms_Helper_Hierarchy::SCOPE_PREFIX_STORE, '', $value);
                        $scope = Enterprise_Cms_Model_Hierarchy_Node::NODE_SCOPE_STORE;
                    }
                    if (!$scopeId || $scopeId == Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
                        $scopeId = Enterprise_Cms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT_ID;
                        $scope = Enterprise_Cms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT;
                    }
                    $nodeHeritageModel->copyTo($scope, $scopeId);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/index', array('website' => $this->_website,
                    'store' => $this->_store));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('enterprise_cms')->__('Error in copying hierarchy.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/index', array('website' => $this->_website,
                    'store' => $this->_store));
                return;
            }
            $this->_getSession()->addSuccess($this->__('Pages Hierarchy have been copied to the selected scopes.'));
        }

        $this->_redirect('*/*/index', array('website' => $this->_website,
            'store' => $this->_store));
        return;
    }

    /**
     * Lock page
     */
    public function lockAction()
    {
        $this->_getLockModel()->lock();
        $this->_redirect('*/*/');
    }

    /**
     * Save changes
     *
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            if (Mage::getModel('enterprise_cms/hierarchy_lock')->isLockedByOther()) {
                $this->_getSession()->addError(
                    Mage::helper('enterprise_cms')->__('This page is currently locked.')
                );
                $this->_redirectReferer();
                return $this;
            }

            $this->_initScope();
            /** @var $node Enterprise_Cms_Model_Hierarchy_Node */
            $node       = Mage::getModel('enterprise_cms/hierarchy_node', array(
                'scope' =>  $this->_scope,
                'scope_id' => $this->_scopeId
            ));
            $data       = $this->getRequest()->getPost();
            $hasError   = true;

            try {
                if (isset($data['use_default_scope_property']) && $data['use_default_scope_property']) {
                    $node->deleteByScope($this->_scope, $this->_scopeId);
                } else {
                    if (!empty($data['nodes_data'])) {
                        try{
                            $nodesData = Mage::helper('core')->jsonDecode($data['nodes_data']);
                        }catch (Zend_Json_Exception $e){
                            $nodesData = array();
                        }
                    } else {
                        $nodesData = array();
                    }
                    if (!empty($data['removed_nodes'])) {
                        $removedNodes = explode(',', $data['removed_nodes']);
                    } else {
                        $removedNodes = array();
                    }

                    // fill in meta_chapter and meta_section based on meta_chapter_section
                    foreach ($nodesData as &$n) {
                        $n['meta_chapter'] = 0;
                        $n['meta_section'] = 0;
                        if (!isset($n['meta_chapter_section'])) {
                            continue;
                        }
                        if ($n['meta_chapter_section'] == 'both' || $n['meta_chapter_section'] == 'chapter') {
                            $n['meta_chapter'] = 1;
                        }
                        if ($n['meta_chapter_section'] == 'both' || $n['meta_chapter_section'] == 'section') {
                            $n['meta_section'] = 1;
                        }
                    }

                    $node->collectTree($nodesData, $removedNodes);
                }

                $hasError = false;
                $this->_getSession()->addSuccess(
                    Mage::helper('enterprise_cms')->__('The hierarchy has been saved.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('enterprise_cms')->__('Error in saving hierarchy.')
                );
                Mage::logException($e);
            }

            if ($hasError) {
                //save data in session
                $this->_getSession()->setFormData($data);
            }
        }

        $this->_redirect('*/*/index', array('website' => $this->_website,
            'store' => $this->_store));
        return;
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
     * Return lock model instance
     *
     * @return Enterprise_Cms_Model_Hierarchy_Lock
     */
    protected function _getLockModel()
    {
        return Mage::getSingleton('enterprise_cms/hierarchy_lock');
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
