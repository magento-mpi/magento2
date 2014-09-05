<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms;

/**
 * Adminhtml Manage Cms Hierarchy Controller
 */
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
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
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
     *
     * @return void
     */
    protected function _initScope()
    {
        $this->_website = $this->getRequest()->getParam('website');
        $this->_store = $this->getRequest()->getParam('store');

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
        if (!$scopeId || $scopeId == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            $scopeId = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_DEFAULT_ID;
            $scope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_DEFAULT;
        }
        return array($scope, $scopeId);
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
