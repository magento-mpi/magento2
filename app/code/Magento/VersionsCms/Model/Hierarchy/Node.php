<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Hierarchy;

/**
 * Cms Hierarchy Pages Node Model
 *
 * @method \Magento\VersionsCms\Model\Resource\Hierarchy\Node getResource()
 * @method int getParentNodeId()
 * @method \Magento\VersionsCms\Model\Hierarchy\Node setParentNodeId(int $value)
 * @method int getPageId()
 * @method \Magento\VersionsCms\Model\Hierarchy\Node setPageId(int $value)
 * @method \Magento\VersionsCms\Model\Hierarchy\Node setIdentifier(string $value)
 * @method \Magento\VersionsCms\Model\Hierarchy\Node setLabel(string $value)
 * @method int getLevel()
 * @method \Magento\VersionsCms\Model\Hierarchy\Node setLevel(int $value)
 * @method int getSortOrder()
 * @method \Magento\VersionsCms\Model\Hierarchy\Node setSortOrder(int $value)
 * @method string getRequestUrl()
 * @method \Magento\VersionsCms\Model\Hierarchy\Node setRequestUrl(string $value)
 * @method string getXpath()
 * @method \Magento\VersionsCms\Model\Hierarchy\Node setXpath(string $value)
 */
class Node extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Meta node's types
     */
    const META_NODE_TYPE_CHAPTER = 'chapter';

    const META_NODE_TYPE_SECTION = 'section';

    const META_NODE_TYPE_FIRST = 'start';

    const META_NODE_TYPE_NEXT = 'next';

    const META_NODE_TYPE_PREVIOUS = 'prev';

    /**
     * Node's scope constants
     */
    const NODE_SCOPE_DEFAULT = 'default';

    const NODE_SCOPE_WEBSITE = 'website';

    const NODE_SCOPE_STORE = 'store';

    const NODE_SCOPE_DEFAULT_ID = 0;

    /**
     * Whether the hierarchy is inherited from parent scope
     *
     * @var null|bool
     */
    protected $_isInherited = null;

    /**
     * Copy collection cache
     *
     * @var array
     */
    protected $_copyCollection = null;

    /**
     * @var array
     */
    protected $_metaNodes = array();

    /**
     * The level of root node for appropriate scope
     */
    const NODE_LEVEL_FAKE = 0;

    /**
     * Node's scope
     * @var string
     */
    protected $_scope = self::NODE_SCOPE_DEFAULT;

    /**
     * Node's scope ID
     *
     * @var int
     */
    protected $_scopeId = self::NODE_SCOPE_DEFAULT_ID;

    /**
     * Tree metadata
     *
     * @var array
     */
    protected $_treeMetaData;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\ConfigInterface
     */
    protected $_hierarchyConfig;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Cms hierarchy
     *
     * @var \Magento\VersionsCms\Helper\Hierarchy
     */
    protected $_cmsHierarchy;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory
     */
    protected $_nodeFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\VersionsCms\Model\Hierarchy\ConfigInterface $hierarchyConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\VersionsCms\Model\Resource\Hierarchy\Node $resource
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\VersionsCms\Model\Hierarchy\NodeFactory $nodeFactory
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
        \Magento\VersionsCms\Model\Hierarchy\ConfigInterface $hierarchyConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\VersionsCms\Model\Resource\Hierarchy\Node $resource,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\VersionsCms\Model\Hierarchy\NodeFactory $nodeFactory,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_cmsHierarchy = $cmsHierarchy;
        $this->_hierarchyConfig = $hierarchyConfig;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_systemStore = $systemStore;
        $this->_nodeFactory = $nodeFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $scope = $scopeId = null;
        if (array_key_exists('scope', $data)) {
            $scope = $data['scope'];
        }

        if (array_key_exists('scope_id', $data)) {
            $scopeId = $data['scope_id'];
        }

        $this->setScope($scope);

        $this->setScopeId($scopeId);
    }

    /**
     * Set nodes scope
     *
     * @param string $scope
     * @return void
     */
    public function setScope($scope)
    {
        if ($scope == self::NODE_SCOPE_STORE || $scope == self::NODE_SCOPE_WEBSITE) {
            $this->_scope = $scope;
        } else {
            $this->_scope = self::NODE_SCOPE_DEFAULT;
        }
    }

    /**
     * Set nodes scope id
     *
     * @param int|string $scopeId
     * @return void
     */
    public function setScopeId($scopeId)
    {
        $collection = array();
        if ($this->_scope == self::NODE_SCOPE_STORE) {
            $collection = $this->_systemStore->getStoreCollection();
        } elseif ($this->_scope == self::NODE_SCOPE_WEBSITE) {
            $collection = $this->_systemStore->getWebsiteCollection();
        }

        $isSet = false;
        foreach ($collection as $scope) {
            if ($scope->getCode() == $scopeId || $scope->getId() == $scopeId) {
                $isSet = true;
                $this->_scopeId = $scope->getId();
            }
        }

        if (!$isSet) {
            $this->_scope = self::NODE_SCOPE_DEFAULT;
            $this->_scopeId = self::NODE_SCOPE_DEFAULT_ID;
        }
    }

    /**
     * Retrieving nodes for appropriate scope and scope ID.
     *
     * @return array
     */
    public function getNodesData()
    {
        $nodes = array();
        $collection = $this->getCollection()->joinCmsPage()->addCmsPageInStoresColumn()->joinMetaData()->applyScope(
            $this->_scope
        )->applyScopeId(
            $this->_scopeId
        )->setOrderByLevel();

        $this->_isInherited = $this->getIsInherited(true);
        foreach ($collection as $item) {
            $this->_isInherited = false;
            if ($item->getLevel() == self::NODE_LEVEL_FAKE) {
                continue;
            }
            /* @var $item \Magento\VersionsCms\Model\Hierarchy\Node */
            $node = array(
                'node_id' => $item->getId(),
                'parent_node_id' => $item->getParentNodeId(),
                'label' => $item->getLabel(),
                'identifier' => $item->getIdentifier(),
                'page_id' => $item->getPageId()
            );
            $nodes[] = $this->_cmsHierarchy->copyMetaData($item->getData(), $node);
        }

        return $nodes;
    }

    /**
     * Retrieving nodes collection for appropriate scope and scope ID.
     *
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function getNodesCollection()
    {
        $collection = $this->getCollection()->joinCmsPage()->addCmsPageInStoresColumn()->joinMetaData()->applyScope(
            $this->_scope
        )->applyScopeId(
            $this->_scopeId
        )->setOrderByLevel();

        return $collection;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\VersionsCms\Model\Resource\Hierarchy\Node');
    }

    /**
     * Collect and save tree
     *
     * @param array $data       modified nodes data array
     * @param array $remove     the removed node ids
     * @return $this
     * @throws \Magento\Framework\Model\Exception|Exception
     */
    public function collectTree($data, $remove)
    {
        if (!is_array($data)) {
            return $this;
        }

        $nodes = array();
        foreach ($data as $v) {
            $required = array('node_id', 'parent_node_id', 'page_id', 'label', 'identifier', 'level', 'sort_order');
            // validate required node data
            foreach ($required as $field) {
                if (!array_key_exists($field, $v)) {
                    throw new \Magento\Framework\Model\Exception(__('Please correct the node data.'));
                }
            }
            $parentNodeId = empty($v['parent_node_id']) ? 0 : $v['parent_node_id'];
            $pageId = empty($v['page_id']) ? null : intval($v['page_id']);


            $_node = array(
                'node_id' => strpos($v['node_id'], '_') === 0 ? null : intval($v['node_id']),
                'page_id' => $pageId,
                'label' => !$pageId ? $v['label'] : null,
                'identifier' => !$pageId ? $v['identifier'] : null,
                'level' => intval($v['level']),
                'sort_order' => intval($v['sort_order']),
                'request_url' => $v['identifier'],
                'scope' => $this->_scope,
                'scope_id' => $this->_scopeId
            );

            $nodes[$parentNodeId][$v['node_id']] = $this->_cmsHierarchy->copyMetaData($v, $_node);
        }

        $this->_getResource()->beginTransaction();
        try {
            // remove deleted nodes
            if (!empty($remove)) {
                $this->_getResource()->dropNodes($remove);
            }
            // recursive node save
            $this->_collectTree($nodes, $this->getId(), $this->getRequestUrl(), $this->getId(), 0);

            $this->_getResource()->addEmptyNode($this->_scope, $this->_scopeId);
            $this->_getResource()->commit();
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Delete Cms Hierarchy of the scope
     *
     * @param string $scope
     * @param int $scopeId
     * @return void
     */
    public function deleteByScope($scope, $scopeId)
    {
        $this->_getResource()->deleteByScope($scope, $scopeId);
    }

    /**
     * Recursive save nodes
     *
     * @param array $nodes
     * @param int $parentNodeId
     * @param string $path
     * @param string $xpath
     * @param int $level
     * @return $this
     */
    protected function _collectTree(array $nodes, $parentNodeId, $path = '', $xpath = '', $level = 0)
    {
        if (!isset($nodes[$level])) {
            return $this;
        }
        foreach ($nodes[$level] as $k => $v) {
            $v['parent_node_id'] = $parentNodeId;
            if ($path != '') {
                $v['request_url'] = $path . '/' . $v['request_url'];
            }

            if ($xpath != '') {
                $v['xpath'] = $xpath . '/';
            } else {
                $v['xpath'] = '';
            }

            $object = clone $this;
            $object->setData($v)->save();

            if (isset($nodes[$k])) {
                $this->_collectTree($nodes, $object->getId(), $object->getRequestUrl(), $object->getXpath(), $k);
            }
        }
        return $this;
    }

    /**
     * Flag to indicate whether append active pages only or not
     *
     * @param bool $flag
     * @return $this
     */
    public function setCollectActivePagesOnly($flag)
    {
        $flag = (bool)$flag;
        $this->setData('collect_active_pages_only', $flag);
        $this->_getResource()->setAppendActivePagesOnly($flag);
        return $this;
    }

    /**
     * Flag to indicate whether append included pages (menu_excluded=0) only or not
     *
     * @param bool $flag
     * @return $this
     */
    public function setCollectIncludedPagesOnly($flag)
    {
        $flag = (bool)$flag;
        $this->setData('collect_included_pages_only', $flag);
        $this->_getResource()->setAppendIncludedPagesOnly($flag);
        return $this;
    }

    /**
     * Retrieve Node or Page identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        $identifier = $this->_getData('identifier');
        if (is_null($identifier)) {
            $identifier = $this->_getData('page_identifier');
        }
        return $identifier;
    }

    /**
     * Is Node used original Page Identifier
     *
     * @return bool
     */
    public function isUseDefaultIdentifier()
    {
        return is_null($this->_getData('identifier'));
    }

    /**
     * Retrieve Node label or Page title
     *
     * @return string
     */
    public function getLabel()
    {
        $label = $this->_getData('label');
        if (is_null($label)) {
            $label = $this->_getData('page_title');
        }
        return $label;
    }

    /**
     * Is Node used original Page Label
     *
     * @return bool
     */
    public function isUseDefaultLabel()
    {
        return is_null($this->_getData('label'));
    }

    /**
     * Load node by Request Url
     *
     * @param string $url
     * @return $this
     */
    public function loadByRequestUrl($url)
    {
        $this->_getResource()->loadByRequestUrl($this, $url);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Retrieve first child node
     *
     * @param int $parentNodeId
     * @return $this
     */
    public function loadFirstChildByParent($parentNodeId)
    {
        $this->_getResource()->loadFirstChildByParent($this, $parentNodeId);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Update rewrite for page (if identifier changed)
     *
     * @param \Magento\Cms\Model\Page $page
     * @return $this
     */
    public function updateRewriteUrls(\Magento\Cms\Model\Page $page)
    {
        $xpaths = $this->_getResource()->getTreeXpathsByPage($page->getId());
        foreach ($xpaths as $xpath) {
            $this->_getResource()->updateRequestUrlsForTreeByXpath($xpath);
        }
        return $this;
    }

    /**
     * Check identifier
     *
     * If a CMS Page belongs to a tree (binded to a tree node), it should not be accessed standalone
     * only by URL that identifies it in a hierarchy.
     *
     * Return true if a page binded to a tree node
     *
     * @param string $identifier
     * @param int|\Magento\Store\Model\Store $storeId
     * @return bool
     */
    public function checkIdentifier($identifier, $storeId = null)
    {
        $storeId = $this->_storeManager->getStore($storeId)->getId();
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Retrieve meta node by specified type for current node's tree.
     * Allowed types:
     *  - chapter       parent node chapter
     *  - section       parent node section
     *  - first         first node in current parent node level
     *  - next          next node (only in current parent node level)
     *  - previous      previous node (only in current parent node level)
     *
     * @param string $type
     * @return $this
     */
    public function getMetaNodeByType($type)
    {
        if (!isset($this->_metaNodes[$type])) {
            /** @var array|bool $data */
            $data = $this->_getResource()->getMetaNodeDataByType($this, $type);
            $model = $this->_nodeFactory->create();
            if ($data !== false) {
                $model->setData($data);
            }

            $this->_metaNodes[$type] = $model;
        }

        return $this->_metaNodes[$type];
    }

    /**
     * Retrieve Page URL
     *
     * @param mixed $store
     * @return string
     */
    public function getUrl($store = null)
    {
        return $this->_storeManager->getStore($store)->getUrl('', array('_direct' => trim($this->getRequestUrl())));
    }

    /**
     * Setter for tree_max_depth data
     * Maximum tree depth for tree slice, if equals zero - no limitations
     *
     * @param int $depth
     * @return $this
     */
    public function setTreeMaxDepth($depth)
    {
        $this->setData('tree_max_depth', (int)$depth);
        return $this;
    }

    /**
     * Setter for tree_is_brief data
     * Tree Detalization, i.e. brief or detailed
     *
     * @param bool $brief
     * @return $this
     */
    public function setTreeIsBrief($brief)
    {
        $this->setData('tree_is_brief', (bool)$brief);
        return $this;
    }

    /**
     * Retrieve Tree Slice like two level array of node models.
     *
     * @param int $up ,if equals zero - no limitation
     * @param int $down ,if equals zero - no limitation
     * @return array
     */
    public function getTreeSlice($up = 0, $down = 0)
    {
        $data = $this->_getResource()->setTreeMaxDepth(
            $this->_getData('tree_max_depth')
        )->setTreeIsBrief(
            $this->_getData('tree_is_brief')
        )->getTreeSlice(
            $this,
            $up,
            $down
        );

        $blankModel = $this->_nodeFactory->create();
        foreach ($data as $parentId => $children) {
            foreach ($children as $childId => $child) {
                $newModel = clone $blankModel;
                $data[$parentId][$childId] = $newModel->setData($child);
            }
        }
        return $data;
    }

    /**
     * Retrieve parent node's children.
     *
     * @return array
     */
    public function getParentNodeChildren()
    {
        $children = $this->_getResource()->getParentNodeChildren($this);
        $blankModel = $this->_nodeFactory->create();
        foreach ($children as $childId => $child) {
            $newModel = clone $blankModel;
            $children[$childId] = $newModel->setData($child);
        }
        return $children;
    }

    /**
     * Load page data for model if defined page id end undefined page data
     *
     * @return $this
     */
    public function loadPageData()
    {
        if ($this->getPageId() && !$this->getPageIdentifier()) {
            $this->_getResource()->loadPageData($this);
        }

        return $this;
    }

    /**
     * Appending passed page as child node for specified nodes and set it specified sort order.
     * Parent nodes specified as array (parentNodeId => sortOrder)
     *
     * @param \Magento\Cms\Model\Page $page
     * @param array $nodes
     * @return $this
     */
    public function appendPageToNodes($page, $nodes)
    {
        $parentNodes = $this->getCollection()->joinPageExistsNodeInfo(
            $page
        )->applyPageExistsOrNodeIdFilter(
            array_keys($nodes),
            $page
        );

        $pageData = array('page_id' => $page->getId(), 'identifier' => null, 'label' => null);

        $removeFromNodes = array();

        foreach ($parentNodes as $node) {
            /* @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
            if (isset($nodes[$node->getId()])) {
                $sortOrder = $nodes[$node->getId()];
                if ($node->getPageExists()) {
                    continue;
                } else {
                    $node->addData(
                        $pageData
                    )->setParentNodeId(
                        $node->getId()
                    )->unsetData(
                        $this->getIdFieldName()
                    )->setLevel(
                        $node->getLevel() + 1
                    )->setSortOrder(
                        $sortOrder
                    )->setRequestUrl(
                        $node->getRequestUrl() . '/' . $page->getIdentifier()
                    )->setXpath(
                        $node->getXpath() . '/'
                    )->save();
                }
            } else {
                $removeFromNodes[] = $node->getId();
            }
        }

        if (!empty($removeFromNodes)) {
            $this->_getResource()->removePageFromNodes($page->getId(), $removeFromNodes);
        }

        return $this;
    }

    /**
     * Get tree meta data flags for current node's tree.
     *
     * @return array|bool
     */
    public function getTreeMetaData()
    {
        if (is_null($this->_treeMetaData)) {
            $this->_treeMetaData = $this->_getResource()->getTreeMetaData($this);
        }

        return $this->_treeMetaData;
    }

    /**
     * Return nearest parent params for node pagination
     *
     * @return array|null
     */
    public function getMetadataPagerParams()
    {
        $values = array(
            \Magento\VersionsCms\Helper\Hierarchy::METADATA_VISIBILITY_YES,
            \Magento\VersionsCms\Helper\Hierarchy::METADATA_VISIBILITY_NO
        );

        return $this->getResource()->getParentMetadataParams($this, 'pager_visibility', $values);
    }

    /**
     * Return nearest parent params for node context menu
     *
     * @return array|null
     */
    public function getMetadataContextMenuParams()
    {
        // Node is excluded from Menu
        if ($this->getData('menu_excluded') == 1) {
            return null;
        }

        // Menu is disabled in some of parent nodes
        $params = $this->getResource()->getParentMetadataParams($this, 'menu_excluded', array(1));
        if ($params !== null && $params['level'] > 1) {
            return null;
        }

        // Root node menu params
        $params = $this->getResource()->getTreeMetaData($this);
        if (isset($params['menu_visibility']) && $params['menu_visibility'] == 1) {
            return $params;
        }

        return null;
    }

    /**
     * Return Hierarchy Menu Layout Info object for Node
     *
     * @return \Magento\Framework\Object|null
     */
    public function getMenuLayout()
    {
        $rootParams = $this->_getResource()->getTreeMetaData($this);
        if (!array_key_exists('menu_layout', $rootParams)) {
            return null;
        }
        $layoutName = $rootParams['menu_layout'];
        if (!$layoutName) {
            $layoutName = $this->_scopeConfig->getValue('cms/hierarchy/menu_layout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        if (!$layoutName) {
            return null;
        }
        $layout = $this->_hierarchyConfig->getContextMenuLayout($layoutName);
        return $layout ? $layout : null;
    }

    /**
     * Process additional data after save.
     *
     * @return $this
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        // we save to metadata table not only metadata :(
        //if ($this->_cmsHierarchy->isMetadataEnabled()) {
        $this->_getResource()->saveMetaData($this);
        //}

        return $this;
    }

    /**
     * Copy Cms Hierarchy to another scope
     *
     * @param string $scope
     * @param int $scopeId
     * @return $this
     */
    public function copyTo($scope, $scopeId)
    {
        if ($this->_scope == $scope && $this->_scopeId == $scopeId) {
            return $this;
        }

        $this->getResource()->deleteByScope($scope, $scopeId);

        if (!$this->_copyCollection) {
            $this->_copyCollection = $this->getCollection()->applyScope(
                $this->_scope
            )->applyScopeId(
                $this->_scopeId
            )->joinCmsPage()->joinMetaData();
        }
        $this->getResource()->copyTo($scope, $scopeId, $this->_copyCollection);
        return $this;
    }

    /**
     * Whether the hierarchy is inherited from parent scope
     *
     * @param bool $soft If true then we will not make requests to the DB and will return true if scope is not default
     * @return bool
     */
    public function getIsInherited($soft = false)
    {
        if (is_null($this->_isInherited)) {
            if ($this->getScope() === self::NODE_SCOPE_DEFAULT) {
                $this->_isInherited = false;
            } elseif (!$soft) {
                $this->_isInherited = $this->getResource()->getIsInherited($this->_scope, $this->_scopeId);
            } else {
                return true;
            }
        }

        return $this->_isInherited;
    }

    /**
     * Get heritage hierarchy
     *
     * @return $this
     */
    public function getHeritage()
    {
        if ($this->getIsInherited()) {
            $helper = $this->_cmsHierarchy;
            $parentScope = $helper->getParentScope($this->_scope, $this->_scopeId);
            $parentScopeNode = $this->_nodeFactory->create(
                array('data' => array('scope' => $parentScope[0], 'scope_id' => $parentScope[1]))
            );
            if ($parentScopeNode->getIsInherited()) {
                $parentScope = $helper->getParentScope($parentScope[0], $parentScope[1]);
                $parentScopeNode = $this->_nodeFactory->create(
                    array('data' => array('scope' => $parentScope[0], 'scope_id' => $parentScope[1]))
                );
            }
            return $parentScopeNode;
        }

        return $this;
    }

    /**
     * Get current scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->_scope;
    }

    /**
     * Get current scopeId
     *
     * @return int
     */
    public function getScopeId()
    {
        return $this->_scopeId;
    }
}
