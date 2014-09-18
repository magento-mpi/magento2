<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Versions cms page observer
 */
namespace Magento\VersionsCms\Model;

use Magento\Framework\Event\Observer as EventObserver;

class Observer
{
    /**
     * Cms hierarchy
     *
     * @var \Magento\VersionsCms\Helper\Hierarchy
     */
    protected $_cmsHierarchy;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory
     */
    protected $_hierarchyNodeFactory;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_coreUrl;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $_view;

    /**
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $coreUrl
     * @param \Magento\Framework\App\ViewInterface $view
     */
    public function __construct(
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $coreUrl,
        \Magento\Framework\App\ViewInterface $view
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_cmsHierarchy = $cmsHierarchy;
        $this->_hierarchyNodeFactory = $hierarchyNodeFactory;
        $this->_storeManager = $storeManager;
        $this->_coreUrl = $coreUrl;
        $this->_view = $view;
    }

    /**
     * Add Hierarchy Menu layout handle to Cms page rendering
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function affectCmsPageRender(EventObserver $observer)
    {
        if (!is_object(
            $this->_coreRegistry->registry('current_cms_hierarchy_node')
        ) || !$this->_cmsHierarchy->isEnabled()
        ) {
            return $this;
        }

        /* @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
        $node = $this->_coreRegistry->registry('current_cms_hierarchy_node');

        /* @var $action \Magento\Framework\App\Action\Action */
        $action = $observer->getEvent()->getControllerAction();

        // collect loaded handles for cms page
        $loadedHandles = $this->_view->getLayout()->getUpdate()->getHandles();

        $menuLayout = $node->getMenuLayout();
        if ($menuLayout === null) {
            return $this;
        }

        // check whether menu handle is compatible with page handles
        $allowedHandles = $menuLayout['pageLayoutHandles'];
        if (is_array($allowedHandles) && count($allowedHandles) > 0) {
            if (count(array_intersect($allowedHandles, $loadedHandles)) == 0) {
                return $this;
            }
        }

        // add menu handle to layout update
        $this->_view->getLayout()->getUpdate()->addHandle($menuLayout['handle']);

        return $this;
    }

    /**
     * Adds CMS hierarchy menu item to top menu
     *
     * @param EventObserver $observer
     * @return void
     */
    public function addCmsToTopmenuItems(EventObserver $observer)
    {
        /**
         * @var $topMenuRootNode \Magento\Framework\Data\Tree\Node
         */
        $topMenuRootNode = $observer->getMenu();

        $hierarchyModel = $this->_hierarchyNodeFactory->create(
            array(
                'data' => array(
                    'scope' => \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE,
                    'scope_id' => $this->_storeManager->getStore()->getId()
                )
            )
        )->getHeritage();

        $nodes = $hierarchyModel->getNodesData();
        $tree = $topMenuRootNode->getTree();

        $nodesFlatList = array($topMenuRootNode->getId() => $topMenuRootNode);

        $nodeModel = $this->_hierarchyNodeFactory->create();

        foreach ($nodes as $node) {

            $nodeData = $nodeModel->load($node['node_id']);

            if (!$nodeData ||
                $nodeData->getParentNodeId() == null && !$nodeData->getTopMenuVisibility() ||
                $nodeData->getParentNodeId() != null && $nodeData->getTopMenuExcluded() ||
                $nodeData->getPageId() && !$nodeData->getPageIsActive()
            ) {
                continue;
            }

            $menuNodeId = 'cms-hierarchy-node-' . $node['node_id'];
            $menuNodeData = array(
                'name' => $nodeData->getLabel(),
                'id' => $menuNodeId,
                'url' => $nodeData->getUrl(),
                'is_active' => $this->_isCmsNodeActive($nodeData)
            );

            $parentNodeId = !isset(
                $node['parent_node_id']
            ) ? $topMenuRootNode->getId() : 'cms-hierarchy-node-' . $node['parent_node_id'];
            $parentNode = isset($nodesFlatList[$parentNodeId]) ? $nodesFlatList[$parentNodeId] : null;

            if (!$parentNode) {
                continue;
            }

            $menuNode = new \Magento\Framework\Data\Tree\Node($menuNodeData, 'id', $tree, $parentNode);
            $parentNode->addChild($menuNode);

            $nodesFlatList[$menuNodeId] = $menuNode;
        }
    }

    /**
     * Validate and render Cms hierarchy page
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function cmsControllerRouterMatchBefore(EventObserver $observer)
    {
        if (!$this->_cmsHierarchy->isEnabled()) {
            return $this;
        }

        $condition = $observer->getEvent()->getCondition();

        /**
         * Validate Request and modify router match condition
         */
        /* @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
        $node = $this->_hierarchyNodeFactory->create(
            array(
                'data' => array(
                    'scope' => \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE,
                    'scope_id' => $this->_storeManager->getStore()->getId()
                )
            )
        )->getHeritage();
        $requestUrl = $condition->getIdentifier();
        $node->loadByRequestUrl($requestUrl);

        if ($node->checkIdentifier($requestUrl, $this->_storeManager->getStore())) {
            $condition->setContinue(false);
            if (!$node->getId()) {
                $collection = $node->getNodesCollection();
                foreach ($collection as $item) {
                    if ($item->getPageIdentifier() == $requestUrl) {
                        $url = $this->_coreUrl->getUrl('', array('_direct' => $item->getRequestUrl()));
                        $condition->setRedirectUrl($url);
                        break;
                    }
                }
            }
        }
        if (!$node->getId()) {
            return $this;
        }

        if (!$node->getPageId()) {
            /* @var $child \Magento\VersionsCms\Model\Hierarchy\Node */
            $child = $this->_hierarchyNodeFactory->create(
                array('data' => array('scope' => $node->getScope(), 'scope_id' => $node->getScopeId()))
            );
            $child->loadFirstChildByParent($node->getId());
            if (!$child->getId()) {
                return $this;
            }
            $url = $this->_coreUrl->getUrl('', array('_direct' => $child->getRequestUrl()));
            $condition->setRedirectUrl($url);
        } else {
            if (!$node->getPageIsActive()) {
                return $this;
            }

            // register hierarchy and node
            $this->_coreRegistry->register('current_cms_hierarchy_node', $node);

            $condition->setContinue(true);
            $condition->setIdentifier($node->getPageIdentifier());
        }

        return $this;
    }

    /**
     * Checks whether node belongs to currently active node's path
     *
     * @param \Magento\VersionsCms\Model\Hierarchy\Node $cmsNode
     * @return bool
     */
    protected function _isCmsNodeActive($cmsNode)
    {
        $currentNode = $this->_coreRegistry->registry('current_cms_hierarchy_node');

        if (!$currentNode) {
            return false;
        }

        $nodePathIds = explode('/', $currentNode->getXpath());

        return in_array($cmsNode->getId(), $nodePathIds);
    }
}
