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
 * Versions cms page observer
 */
class Magento_VersionsCms_Model_Observer
{
    /**
     * Configuration model
     *
     * @var Magento_VersionsCms_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Cms hierarchy
     *
     * @var Magento_VersionsCms_Helper_Hierarchy
     */
    protected $_cmsHierarchy;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Backend_Model_Config_Source_Yesno
     */
    protected $_sourceYesno;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_backendAuthSession;

    /**
     * @var Magento_Backend_Model_Url
     */
    protected $_backendUrl;

    /**
     * @var Magento_Core_Model_Url
     */
    protected $_coreUrl;

    /**
     * @var Magento_VersionsCms_Model_Page_RevisionFactory
     */
    protected $_revisionFactory;

    /**
     * @var Magento_VersionsCms_Model_Hierarchy_NodeFactory
     */
    protected $_hierarchyNodeFactory;

    /**
     * @var Magento_VersionsCms_Model_Hierarchy_Node
     */
    protected $_hierarchyNode;

    /**
     * @var Magento_VersionsCms_Model_Page_VersionFactory
     */
    protected $_pageVersionFactory;

    /**
     * @var Magento_VersionsCms_Model_Resource_Page_Version_CollectionFactory
     */
    protected $_versionCollFactory;

    /**
     * @var Magento_Core_Model_Resource_Iterator
     */
    protected $_resourceIterator;

    /**
     * @var Magento_Widget_Model_Resource_Widget_Instance_CollectionFactory
     */
    protected $_widgetCollFactory;

    /**
     * @var Magento_VersionsCms_Model_Resource_Hierarchy_Node
     */
    protected $_hierarchyNodeResource;

    /**
     * @var Magento_VersionsCms_Model_Resource_Increment
     */
    protected $_cmsIncrement;

    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_httpRequest;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_VersionsCms_Helper_Hierarchy $cmsHierarchy
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_VersionsCms_Model_Config $config
     * @param Magento_AuthorizationInterface $authorization
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Backend_Model_Config_Source_Yesno $sourceYesno
     * @param Magento_Backend_Model_Auth_Session $backendAuthSession
     * @param Magento_Backend_Model_Url $backendUrl
     * @param Magento_Core_Model_Url $coreUrl
     * @param Magento_VersionsCms_Model_Page_RevisionFactory $revisionFactory
     * @param Magento_VersionsCms_Model_Hierarchy_NodeFactory $hierarchyNodeFactory
     * @param Magento_VersionsCms_Model_Hierarchy_Node $hierarchyNode
     * @param Magento_VersionsCms_Model_Page_VersionFactory $pageVersionFactory
     * @param Magento_VersionsCms_Model_Resource_Page_Version_CollectionFactory $versionCollFactory
     * @param Magento_Core_Model_Resource_Iterator $resourceIterator
     * @param Magento_Widget_Model_Resource_Widget_Instance_CollectionFactory $widgetCollFactory
     * @param Magento_VersionsCms_Model_Resource_Hierarchy_Node $hierarchyNodeResource
     * @param Magento_VersionsCms_Model_Resource_Increment $cmsIncrement
     * @param Magento_Core_Controller_Request_Http $httpRequest
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_VersionsCms_Helper_Hierarchy $cmsHierarchy,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_VersionsCms_Model_Config $config,
        Magento_AuthorizationInterface $authorization,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Backend_Model_Config_Source_Yesno $sourceYesno,
        Magento_Backend_Model_Auth_Session $backendAuthSession,
        Magento_Backend_Model_Url $backendUrl,
        Magento_Core_Model_Url $coreUrl,
        Magento_VersionsCms_Model_Page_RevisionFactory $revisionFactory,
        Magento_VersionsCms_Model_Hierarchy_NodeFactory $hierarchyNodeFactory,
        Magento_VersionsCms_Model_Hierarchy_Node $hierarchyNode,
        Magento_VersionsCms_Model_Page_VersionFactory $pageVersionFactory,
        Magento_VersionsCms_Model_Resource_Page_Version_CollectionFactory $versionCollFactory,
        Magento_Core_Model_Resource_Iterator $resourceIterator,
        Magento_Widget_Model_Resource_Widget_Instance_CollectionFactory $widgetCollFactory,
        Magento_VersionsCms_Model_Resource_Hierarchy_Node $hierarchyNodeResource,
        Magento_VersionsCms_Model_Resource_Increment $cmsIncrement,
        Magento_Core_Controller_Request_Http $httpRequest
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_coreData = $coreData;
        $this->_cmsHierarchy = $cmsHierarchy;
        $this->_config = $config;
        $this->_authorization = $authorization;
        $this->_storeManager = $storeManager;
        $this->_sourceYesno = $sourceYesno;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_backendUrl = $backendUrl;
        $this->_coreUrl = $coreUrl;
        $this->_revisionFactory = $revisionFactory;
        $this->_hierarchyNodeFactory = $hierarchyNodeFactory;
        $this->_hierarchyNode = $hierarchyNode;
        $this->_pageVersionFactory = $pageVersionFactory;
        $this->_versionCollFactory = $versionCollFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->_widgetCollFactory = $widgetCollFactory;
        $this->_hierarchyNodeResource = $hierarchyNodeResource;
        $this->_cmsIncrement = $cmsIncrement;
        $this->_httpRequest = $httpRequest;
    }

    /**
     * Making changes to main tab regarding to custom logic
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_VersionsCms_Model_Observer
     */
    public function onMainTabPrepareForm($observer)
    {
        $form = $observer->getEvent()->getForm();
        /* @var $baseFieldset Magento_Data_Form_Element_Fieldset */
        $baseFieldset = $form->getElement('base_fieldset');
        /* @var $baseFieldset Magento_Data_Form_Element_Fieldset */

        $isActiveElement = $form->getElement('is_active');
        if ($isActiveElement) {
            // Making is_active as disabled if user does not have publish permission
            if (!$this->_config->canCurrentUserPublishRevision()) {
                    $isActiveElement->setDisabled(true);
            }
        }

        /*
         * Adding link to current published revision
         */
        /* @var $page Magento_VersionsCms_Model_Page */
        $page = $this->_coreRegistry->registry('cms_page');
        $revisionAvailable = false;
        if ($page) {

            $baseFieldset->addField('under_version_control', 'select', array(
                'label'     => __('Under Version Control'),
                'title'     => __('Under Version Control'),
                'name'      => 'under_version_control',
                'values'    => $this->_sourceYesno->toOptionArray()
            ));

            if ($page->getPublishedRevisionId() && $page->getUnderVersionControl()) {
                $userId = $this->_backendAuthSession->getUser()->getId();
                $accessLevel = $this->_config->getAllowedAccessLevel();

                /** @var Magento_VersionsCms_Model_Page_Revision $revision */
                $revision = $this->_revisionFactory->create()
                    ->loadWithRestrictions($accessLevel, $userId, $page->getPublishedRevisionId());

                if ($revision->getId()) {
                    $revisionNumber = $revision->getRevisionNumber();
                    $versionLabel = $revision->getLabel();

                    $page->setPublishedRevisionLink(__('%1; rev #%2', $versionLabel, $revisionNumber));

                    $baseFieldset->addField('published_revision_link', 'link', array(
                        'label' => __('Currently Published Revision'),
                        'href' => $this->_backendUrl->getUrl('*/cms_page_revision/edit', array(
                            'page_id' => $page->getId(),
                            'revision_id' => $page->getPublishedRevisionId()
                        )),
                    ));

                    $revisionAvailable = true;
                }
            }
        }

        if ($revisionAvailable && !$this->_authorization->isAllowed('Magento_VersionsCms::save_revision')) {
            foreach ($baseFieldset->getElements() as $element) {
                $element->setDisabled(true);
            }
        }

        /**
         * User does not have access to revision or revision is no longer available
         */
        if (!$revisionAvailable && $page->getId() && $page->getUnderVersionControl()) {
            $baseFieldset->addField('published_revision_status', 'label', array('bold' => true));
            $page->setPublishedRevisionStatus(__('The published revision is unavailable.'));
        }

        return $this;
    }

    /**
     * Validate and render Cms hierarchy page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_VersionsCms_Model_Observer
     */
    public function cmsControllerRouterMatchBefore(Magento_Event_Observer $observer)
    {
        if (!$this->_cmsHierarchy->isEnabled()) {
            return $this;
        }

        $condition = $observer->getEvent()->getCondition();

        /**
         * Validate Request and modify router match condition
         */
        /* @var $node Magento_VersionsCms_Model_Hierarchy_Node */
        $node = $this->_hierarchyNodeFactory->create(array(
            'data' => array(
                'scope' => Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_STORE,
                'scope_id' => $this->_storeManager->getStore()->getId(),
        )))->getHeritage();
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
            /* @var $child Magento_VersionsCms_Model_Hierarchy_Node */
            $child = $this->_hierarchyNodeFactory->create(array(
                'data' => array(
                    'scope' => $node->getScope(),
                    'scope_id' => $node->getScopeId(),
            )));
            $child->loadFirstChildByParent($node->getId());
            if (!$child->getId()) {
                return $this;
            }
            $url   = $this->_coreUrl->getUrl('', array('_direct' => $child->getRequestUrl()));
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
     * Processing extra data after cms page saved
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_VersionsCms_Model_Observer
     */
    public function cmsPageSaveAfter(Magento_Event_Observer $observer)
    {
        /* @var $page Magento_Cms_Model_Page */
        $page = $observer->getEvent()->getObject();

        // Create new initial version & revision if it
        // is a new page or version control was turned on for this page.
        if ($page->getIsNewPage() || ($page->getUnderVersionControl()
            && $page->dataHasChangedFor('under_version_control'))
        ) {
            /** @var Magento_VersionsCms_Model_Page_Version $version */
            $version = $this->_pageVersionFactory->create();

            $revisionInitialData = $page->getData();
            $revisionInitialData['copied_from_original'] = true;

            $version->setLabel($page->getTitle())
                ->setAccessLevel(Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PUBLIC)
                ->setPageId($page->getId())
                ->setUserId($this->_backendAuthSession->getUser()->getId())
                ->setInitialRevisionData($revisionInitialData)
                ->save();

            if ($page->getUnderVersionControl()) {
                $revision = $version->getLastRevision();

                if ($revision instanceof Magento_VersionsCms_Model_Page_Revision) {
                    $revision->publish();
                }
            }
        }

        if (!$this->_cmsHierarchy->isEnabled()) {
            return $this;
        }

        // rebuild URL rewrites if page has changed for identifier
        if ($page->dataHasChangedFor('identifier')) {
            $this->_hierarchyNode->updateRewriteUrls($page);
        }

        /**
         * Appending page to selected nodes it will remove pages from other nodes
         * which are not specified in array. So should be called even array is empty!
         * Returns array of new ids for page nodes array( oldId => newId ).
         */
        $this->_hierarchyNode->appendPageToNodes($page, $page->getAppendToNodes());

        /**
         * Updating sort order for nodes in parent nodes which have current page as child
         */
        foreach ($page->getNodesSortOrder() as $nodeId => $value) {
            $this->_hierarchyNodeResource->updateSortOrder($nodeId, $value);
        }

        return $this;
    }

    /**
     * Preparing cms page object before it will be saved
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_VersionsCms_Model_Observer
     */
    public function cmsPageSaveBefore(Magento_Event_Observer $observer)
    {
        /* @var Magento_Cms_Model_Page $page */
        $page = $observer->getEvent()->getObject();
        /*
         * All new pages created by user without permission to publish
         * should be disabled from the beginning.
         */
        if (!$page->getId()) {
            $page->setIsNewPage(true);
            if (!$this->_config->canCurrentUserPublishRevision()) {
                $page->setIsActive(false);
            }
            // newly created page should be auto assigned to website root
            $page->setWebsiteRoot(true);
        } elseif (!$page->getUnderVersionControl()) {
            $page->setPublishedRevisionId(null);
        }

        /*
         * Checking if node's data was passed and if yes. Saving new sort order for nodes.
         */
        $nodesData = $page->getNodesData();
        $appendToNodes = array();
        $sortOrder = array();
        if ($nodesData) {
            try {
                $nodesData = $this->_coreData->jsonDecode($page->getNodesData());
            } catch (Zend_Json_Exception $e) {
                $nodesData=null;
            }
            if (!empty($nodesData)) {
                foreach ($nodesData as $row) {
                    if (isset($row['page_exists']) && $row['page_exists']) {
                        $appendToNodes[$row['node_id']] = 0;
                    }

                    if (isset($appendToNodes[$row['parent_node_id']])) {
                        if (strpos($row['node_id'], '_') !== false) {
                            $appendToNodes[$row['parent_node_id']] = $row['sort_order'];
                        } else {
                            $sortOrder[$row['node_id']] = $row['sort_order'];
                        }
                    }
                }
            }
        }

        $page->setNodesSortOrder($sortOrder);
        $page->setAppendToNodes($appendToNodes);
        return $this;
    }

    /**
     * Clean up private versions after user deleted.
     *
     * @return Magento_VersionsCms_Model_Observer
     */
    public function adminUserDeleteAfter()
    {
        /** @var Magento_VersionsCms_Model_Resource_Page_Version_Collection $collection */
        $collection = $this->_versionCollFactory->create()
            ->addAccessLevelFilter(Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PRIVATE)
            ->addUserIdFilter();

        $this->_resourceIterator->walk(
            $collection->getSelect(),
            array(array($this, 'removeVersionCallback')),
            array('version' => $this->_pageVersionFactory->create())
        );

         return $this;
    }

    /**
     * Clean up hierarchy tree that belongs to website.
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_VersionsCms_Model_Observer
     */
    public function deleteWebsite(Magento_Event_Observer $observer)
    {
        /* @var $store Magento_Core_Model_Website */
        $website = $observer->getEvent()->getWebsite();

        $this->_hierarchyNodeFactory->create()
            ->deleteByScope(Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_WEBSITE, $website->getId());

        foreach ($website->getStoreIds() as $storeId) {
            $this->_cleanStoreFootprints($storeId);
        }

        return $this;
    }

    /**
     * Clean up hierarchy tree that belongs to store.
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_VersionsCms_Model_Observer
     */
    public function deleteStore(Magento_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getStore()->getId();
        $this->_cleanStoreFootprints($storeId);
        return $this;
    }

    /**
     * Clean up information about deleted store from the widgets and hierarchy nodes
     *
     * @param int $storeId
     */
    private function _cleanStoreFootprints($storeId)
    {
        $storeScope = Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_STORE;
        $this->_hierarchyNodeFactory->create()->deleteByScope($storeScope, $storeId);

        /** @var Magento_Widget_Model_Resource_Widget_Instance_Collection $widgets */
        $widgets = $this->_widgetCollFactory->create()
                ->addStoreFilter(array($storeId, false))
                ->addFieldToFilter('instance_type', 'Magento_VersionsCms_Block_Widget_Node');

        /* @var $widgetInstance Magento_Widget_Model_Widget_Instance */
        foreach ($widgets as $widgetInstance) {
            $storeIds = $widgetInstance->getStoreIds();
            foreach ($storeIds as $key => $value) {
                if ($value == $storeId) {
                    unset($storeIds[$key]);
                }
            }
            $widgetInstance->setStoreIds($storeIds);

            $widgetParams = $widgetInstance->getWidgetParameters();
            unset($widgetParams['anchor_text_' . $storeId]);
            unset($widgetParams['title_' . $storeId]);
            unset($widgetParams['node_id_' . $storeId]);
            $widgetInstance->setWidgetParameters($widgetParams);

            $widgetInstance->save();
        }
    }

    /**
     * Callback function to remove version or change access
     * level to protected if we can't remove it.
     *
     * @param array $args
     */
    public function removeVersionCallback($args)
    {
        $version = $args['version'];
        $version->setData($args['row']);

        try {
            $version->delete();
        } catch (Magento_Core_Exception $e) {
            // If we have situation when revision from
            // orphaned private version published we should
            // change its access level to protected so publisher
            // will have chance to see it and assign to some user
            $version->setAccessLevel(Magento_VersionsCms_Model_Page_Version::ACCESS_LEVEL_PROTECTED);
            $version->save();
        }
    }

    /**
     * Removing unneeded data from increment table for removed page.
     *
     * @param $observer
     * @return Magento_VersionsCms_Model_Observer
     */
    public function cmsPageDeleteAfter(Magento_Event_Observer $observer)
    {
        /* @var $page Magento_Cms_Model_Page */
        $page = $observer->getEvent()->getObject();

        $this->_cmsIncrement->cleanIncrementRecord(
            Magento_VersionsCms_Model_Increment::TYPE_PAGE,
            $page->getId(),
            Magento_VersionsCms_Model_Increment::LEVEL_VERSION
        );

        return $this;
    }

    /**
     * Handler for cms hierarchy view
     *
     * @param array $config
     * @param Magento_Logging_Model_Event $eventModel
     * @return Magento_Logging_Model_Event|false
     */
    public function postDispatchCmsHierachyView($config, $eventModel)
    {
        return $eventModel->setInfo(__('Tree Viewed'));
    }

    /**
     * Handler for cms revision preview
     *
     * @param array $config
     * @param Magento_Logging_Model_Event $eventModel
     * @return Magento_Logging_Model_Event|false
     */
    public function postDispatchCmsRevisionPreview($config, $eventModel)
    {
        return $eventModel->setInfo($this->_httpRequest->getParam('revision_id'));
    }

    /**
     * Handler for cms revision publish
     *
     * @param array $config
     * @param Magento_Logging_Model_Event $eventModel
     * @return Magento_Logging_Model_Event|false
     */
    public function postDispatchCmsRevisionPublish($config, $eventModel)
    {
        return $eventModel->setInfo($this->_httpRequest->getParam('revision_id'));
    }

    /**
     * Add Hierarchy Menu layout handle to Cms page rendering
     *
     * @param $observer
     * @return Magento_VersionsCms_Model_Observer
     */
    public function affectCmsPageRender(Magento_Event_Observer $observer)
    {
        if (!is_object($this->_coreRegistry->registry('current_cms_hierarchy_node'))
            || !$this->_cmsHierarchy->isEnabled()
        ) {
            return $this;
        }

        /* @var $node Magento_VersionsCms_Model_Hierarchy_Node */
        $node = $this->_coreRegistry->registry('current_cms_hierarchy_node');

        /* @var $action Magento_Core_Controller_Varien_Action */
        $action = $observer->getEvent()->getControllerAction();

        // collect loaded handles for cms page
        $loadedHandles = $action->getLayout()->getUpdate()->getHandles();

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
        $action->getLayout()->getUpdate()->addHandle($menuLayout['handle']);

        return $this;
    }

    /**
     * Adds CMS hierarchy menu item to top menu
     *
     * @param Magento_Event_Observer $observer
     */
    public function addCmsToTopmenuItems(Magento_Event_Observer $observer)
    {
        /**
         * @var $topMenuRootNode Magento_Data_Tree_Node
         */
        $topMenuRootNode = $observer->getMenu();

        $hierarchyModel = $this->_hierarchyNodeFactory->create(array(
            'data' => array(
                'scope' => Magento_VersionsCms_Model_Hierarchy_Node::NODE_SCOPE_STORE,
                'scope_id' => $this->_storeManager->getStore()->getId(),
        )))->getHeritage();

        $nodes = $hierarchyModel->getNodesData();
        $tree = $topMenuRootNode->getTree();

        $nodesFlatList = array(
            $topMenuRootNode->getId() => $topMenuRootNode
        );

        $nodeModel = $this->_hierarchyNodeFactory->create();

        foreach ($nodes as $node) {

            $nodeData = $nodeModel->load($node['node_id']);

            if (!$nodeData || ($nodeData->getParentNodeId() == null && !$nodeData->getTopMenuVisibility())
                || ($nodeData->getParentNodeId() != null && $nodeData->getTopMenuExcluded())
                || ($nodeData->getPageId() && !$nodeData->getPageIsActive())
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

            $parentNodeId = !isset($node['parent_node_id']) ? $topMenuRootNode->getId()
                : 'cms-hierarchy-node-' . $node['parent_node_id'];
            $parentNode = isset($nodesFlatList[$parentNodeId]) ? $nodesFlatList[$parentNodeId] : null;

            if (!$parentNode) {
                continue;
            }

            $menuNode = new Magento_Data_Tree_Node($menuNodeData, 'id', $tree, $parentNode);
            $parentNode->addChild($menuNode);

            $nodesFlatList[$menuNodeId] = $menuNode;
        }
    }

    /**
     * Checks whether node belongs to currently active node's path
     *
     * @param Magento_VersionsCms_Model_Hierarchy_Node $cmsNode
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
