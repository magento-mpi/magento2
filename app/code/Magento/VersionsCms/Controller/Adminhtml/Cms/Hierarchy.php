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
 * Admihtml Manage Cms Hierarchy Controller
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Controller_Adminhtml_Cms_Hierarchy extends Magento_Adminhtml_Controller_Action
{
    /**
     * Current Scope
     *
     * @var string
     */
    protected $_scope = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT;

    /**
     * Current ScopeId
     *
     * @var int
     */
    protected $_scopeId = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT_ID;

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
     * @return Magento_VersionsCms_HierarchyController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::helper('Magento_VersionsCms_Helper_Hierarchy')->isEnabled()) {
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
            $this->_scope = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_WEBSITE;
            $website = Mage::app()->getWebsite($this->_website);
            $this->_scopeId = $website->getId();
            $this->_website = $website->getCode();
        }

        if (!is_null($this->_store)) {
            $this->_scope = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_STORE;
            $store = Mage::app()->getStore($this->_store);
            $this->_scopeId = $store->getId();
            $this->_store = $store->getCode();
        }
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return Magento_VersionsCms_HierarchyController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_VersionsCms::cms_enterprise_page_hierarchy')
            ->_addBreadcrumb(__('CMS'),
                __('CMS'))
            ->_addBreadcrumb(__('CMS Page Trees'),
                __('CMS Page Trees'));
        return $this;
    }

    /**
     * Retrieve Scope and ScopeId from string with prefix
     *
     * @param string $value
     * @return array
     */
    protected function _getScopeData($value)
    {
        $scopeId = false;
        $scope = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT;
        if (0 === strpos($value, Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_WEBSITE)) {
            $scopeId = (int)str_replace(Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_WEBSITE, '', $value);
            $scope = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_WEBSITE;
        } elseif (0 === strpos($value, Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_STORE)) {
            $scopeId = (int)str_replace(Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_STORE, '', $value);
            $scope = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_STORE;
        }
        if (!$scopeId || $scopeId == Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
            $scopeId = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT_ID;
            $scope = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_DEFAULT;
        }
        return array($scope, $scopeId);
    }

    /**
     * Show Tree Edit Page
     *
     * @return null
     */
    public function indexAction()
    {
        $this->_title(__('Hierarchy'));

        $this->_initScope();

        $nodeModel = Mage::getModel('Magento_VersionsCms_Model_Hierarchy_Node', array('data' =>
                array('scope' => $this->_scope, 'scope_id' => $this->_scopeId)));

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
     * Delete hierarchy from one or several scopes
     *
     * @return null
     */
    public function deleteAction()
    {
        $this->_initScope();
        $scopes = $this->getRequest()->getParam('scopes');
        if (empty($scopes) || ($this->getRequest()->isPost() && !is_array($scopes))
            || $this->getRequest()->isGet() && !is_string($scopes)
        ) {
            $this->_getSession()->addError(__('Please correct the scope.'));
        } else {
            if (!is_array($scopes)) {
                $scopes = array($scopes);
            }
            try {
                /* @var $nodeModel Magento_VersionsCms_Model_Hierarchy_Node */
                $nodeModel = Mage::getModel('Magento_VersionsCms_Model_Hierarchy_Node');
                foreach (array_unique($scopes) as $value) {
                    list ($scope, $scopeId) = $this->_getScopeData($value);
                    $nodeModel->setScope($scope);
                    $nodeModel->setScopeId($scopeId);
                    $nodeModel->deleteByScope($scope, $scopeId);
                    $nodeModel->collectTree(array(), array());
                }
                $this->_getSession()->addSuccess(__('You deleted the pages hierarchy from the selected scopes.'));
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    __('Something went wrong while deleting the hierarchy.')
                );
            }
        }

        $this->_redirect('*/*/index', array('website' => $this->_website, 'store' => $this->_store));
        return;
    }

    /**
     * Copy hierarchy from one scope to other scopes
     *
     * @return null
     */
    public function copyAction()
    {
        $this->_initScope();
        $scopes = $this->getRequest()->getParam('scopes');
        if ($this->getRequest()->isPost() && is_array($scopes) && !empty($scopes)) {
            /** @var $nodeModel Magento_VersionsCms_Model_Hierarchy_Node */
            $nodeModel = Mage::getModel('Magento_VersionsCms_Model_Hierarchy_Node', array(
                'data' => array('scope'    => $this->_scope,
                                'scope_id' => $this->_scopeId)
            ));
            $nodeHeritageModel = $nodeModel->getHeritage();
            try {
                foreach (array_unique($scopes) as $value) {
                    list ($scope, $scopeId) = $this->_getScopeData($value);
                    $nodeHeritageModel->copyTo($scope, $scopeId);
                }
                $this->_getSession()->addSuccess(__('You copied the pages hierarchy to the selected scopes.'));
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    __('Something went wrong while copying the hierarchy.')
                );
            }
        }

        $this->_redirect('*/*/index', array('website' => $this->_website, 'store' => $this->_store));
        return;
    }

    /**
     * Lock page
     * @deprecated since 1.12.0.0
     */
    public function lockAction()
    {
        $this->_redirect('*/*/');
    }

    /**
     * Save changes
     *
     * @return null
     */
    public function saveAction()
    {
        $this->_initScope();
        if ($this->getRequest()->isPost()) {
            /** @var $node Magento_VersionsCms_Model_Hierarchy_Node */
            $node       = Mage::getModel('Magento_VersionsCms_Model_Hierarchy_Node', array(
                'data' => array('scope'    => $this->_scope,
                                'scope_id' => $this->_scopeId)
            ));
            $data       = $this->getRequest()->getPost();
            $hasError   = true;

            try {
                if (isset($data['use_default_scope_property']) && $data['use_default_scope_property']) {
                    $node->deleteByScope($this->_scope, $this->_scopeId);
                } else {
                    if (!empty($data['nodes_data'])) {
                        try{
                            $nodesData = Mage::helper('Magento_Core_Helper_Data')->jsonDecode($data['nodes_data']);
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
                    __('You have saved the hierarchy.')
                );
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    __('Something went wrong while saving the hierarchy.')
                );
            }

            if ($hasError) {
                //save data in session
                $this->_getSession()->setFormData($data);
            }
        }

        $this->_redirect('*/*/index', array('website' => $this->_website, 'store' => $this->_store));
        return;
    }

    /**
     * Cms Pages Ajax Grid
     *
     * @return null
     */
    public function pageGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Return lock model instance
     *
     * @deprecated since 1.12.0.0
     * @return Magento_VersionsCms_Model_Hierarchy_Lock
     */
    protected function _getLockModel()
    {
        return Mage::getSingleton('Magento_VersionsCms_Model_Hierarchy_Lock');
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_VersionsCms::hierarchy');
    }
}
