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
 * Adminhtml Manage Cms Hierarchy Controller
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms;

class Hierarchy extends \Magento\Backend\App\Action
{
    /**
     * Current Scope
     *
     * @var string
     */
    protected $_scope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_DEFAULT;

    /**
     * Current ScopeId
     *
     * @var int
     */
    protected $_scopeId = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_DEFAULT_ID;

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
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(\Magento\App\RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\VersionsCms\Helper\Hierarchy')->isEnabled()) {
            if ($request->getActionName() != 'noroute') {
                $this->_forward('noroute');
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Init scope and scope code by website and store for actions
     */
    protected function _initScope()
    {
        $this->_website = $this->getRequest()->getParam('website');
        $this->_store   = $this->getRequest()->getParam('store');

        if (!is_null($this->_website)) {
            $this->_scope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_WEBSITE;
            $website = $this->_storeManager->getWebsite($this->_website);
            $this->_scopeId = $website->getId();
            $this->_website = $website->getCode();
        }

        if (!is_null($this->_store)) {
            $this->_scope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE;
            $store = $this->_storeManager->getStore($this->_store);
            $this->_scopeId = $store->getId();
            $this->_store = $store->getCode();
        }
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return \Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_VersionsCms::versionscms_page_hierarchy')
            ->_addBreadcrumb(__('CMS'), __('CMS'))
            ->_addBreadcrumb(__('CMS Page Trees'), __('CMS Page Trees'));
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
        $scope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_DEFAULT;
        if (0 === strpos($value, \Magento\VersionsCms\Helper\Hierarchy::SCOPE_PREFIX_WEBSITE)) {
            $scopeId = (int)str_replace(\Magento\VersionsCms\Helper\Hierarchy::SCOPE_PREFIX_WEBSITE, '', $value);
            $scope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_WEBSITE;
        } elseif (0 === strpos($value, \Magento\VersionsCms\Helper\Hierarchy::SCOPE_PREFIX_STORE)) {
            $scopeId = (int)str_replace(\Magento\VersionsCms\Helper\Hierarchy::SCOPE_PREFIX_STORE, '', $value);
            $scope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE;
        }
        if (!$scopeId || $scopeId == \Magento\Core\Model\Store::DEFAULT_STORE_ID) {
            $scopeId = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_DEFAULT_ID;
            $scope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_DEFAULT;
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
        $this->_title->add(__('Hierarchy'));

        $this->_initScope();

        $nodeModel = $this->_objectManager->create('Magento\VersionsCms\Model\Hierarchy\Node', array('data' =>
                array('scope' => $this->_scope, 'scope_id' => $this->_scopeId)));

        // restore data if exists
        $formData = $this->_getSession()->getFormData(true);
        if (!empty($formData)) {
            $nodeModel->addData($formData);
            unset($formData);
        }

        $this->_coreRegistry->register('current_hierarchy_node', $nodeModel);

        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * Delete hierarchy from one or several scopes
     */
    public function deleteAction()
    {
        $this->_initScope();
        $scopes = $this->getRequest()->getParam('scopes');
        if (empty($scopes) || ($this->getRequest()->isPost() && !is_array($scopes))
            || $this->getRequest()->isGet() && !is_string($scopes)
        ) {
            $this->messageManager->addError(__('Please correct the scope.'));
        } else {
            if (!is_array($scopes)) {
                $scopes = array($scopes);
            }
            try {
                /* @var $nodeModel \Magento\VersionsCms\Model\Hierarchy\Node */
                $nodeModel = $this->_objectManager->create('Magento\VersionsCms\Model\Hierarchy\Node');
                foreach (array_unique($scopes) as $value) {
                    list ($scope, $scopeId) = $this->_getScopeData($value);
                    $nodeModel->setScope($scope);
                    $nodeModel->setScopeId($scopeId);
                    $nodeModel->deleteByScope($scope, $scopeId);
                    $nodeModel->collectTree(array(), array());
                }
                $this->messageManager->addSuccess(__('You deleted the pages hierarchy from the selected scopes.'));
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e,
                    __('Something went wrong while deleting the hierarchy.')
                );
            }
        }

        $this->_redirect('adminhtml/*/index', array('website' => $this->_website, 'store' => $this->_store));
        return;
    }

    /**
     * Copy hierarchy from one scope to other scopes
     */
    public function copyAction()
    {
        $this->_initScope();
        $scopes = $this->getRequest()->getParam('scopes');
        if ($this->getRequest()->isPost() && is_array($scopes) && !empty($scopes)) {
            /** @var $nodeModel \Magento\VersionsCms\Model\Hierarchy\Node */
            $nodeModel = $this->_objectManager->create('Magento\VersionsCms\Model\Hierarchy\Node', array(
                'data' => array('scope'    => $this->_scope,
                                'scope_id' => $this->_scopeId)
            ));
            $nodeHeritageModel = $nodeModel->getHeritage();
            try {
                foreach (array_unique($scopes) as $value) {
                    list ($scope, $scopeId) = $this->_getScopeData($value);
                    $nodeHeritageModel->copyTo($scope, $scopeId);
                }
                $this->messageManager->addSuccess(__('You copied the pages hierarchy to the selected scopes.'));
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e,
                    __('Something went wrong while copying the hierarchy.')
                );
            }
        }

        $this->_redirect('adminhtml/*/index', array('website' => $this->_website, 'store' => $this->_store));
        return;
    }

    /**
     * Lock page
     * @deprecated since 1.12.0.0
     */
    public function lockAction()
    {
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Save changes
     */
    public function saveAction()
    {
        $this->_initScope();
        if ($this->getRequest()->isPost()) {
            /** @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
            $node = $this->_objectManager->create('Magento\VersionsCms\Model\Hierarchy\Node', array(
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
                            $nodesData = $this->_objectManager->get('Magento\Core\Helper\Data')
                                ->jsonDecode($data['nodes_data']);
                        } catch (\Zend_Json_Exception $e) {
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
                $this->messageManager->addSuccess(__('You have saved the hierarchy.'));
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e,
                    __('Something went wrong while saving the hierarchy.')
                );
            }

            if ($hasError) {
                //save data in session
                $this->_getSession()->setFormData($data);
            }
        }

        $this->_redirect('adminhtml/*/index', array('website' => $this->_website, 'store' => $this->_store));
        return;
    }

    /**
     * Cms Pages Ajax Grid
     *
     * @return null
     */
    public function pageGridAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Return lock model instance
     *
     * @deprecated since 1.12.0.0
     * @return \Magento\VersionsCms\Model\Hierarchy\Lock
     */
    protected function _getLockModel()
    {
        return $this->_objectManager->get('Magento\VersionsCms\Model\Hierarchy\Lock');
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
