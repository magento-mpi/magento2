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
 * Enterprise cms page observer
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\VersionsCms\Model;

class Observer
{
    /**
     * Configuration model
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Cms hierarchy
     *
     * @var \Magento\VersionsCms\Helper\Hierarchy
     */
    protected $_cmsHierarchy = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\VersionsCms\Model\Config $config
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\VersionsCms\Model\Config $config,
        \Magento\AuthorizationInterface $authorization
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_coreData = $coreData;
        $this->_cmsHierarchy = $cmsHierarchy;
        $this->_config = $config;
        $this->_authorization = $authorization;
    }

    /**
     * Making changes to main tab regarding to custom logic
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    public function onMainTabPrepareForm($observer)
    {
        $form = $observer->getEvent()->getForm();
        /* @var $baseFieldset \Magento\Data\Form\Element\Fieldset */
        $baseFieldset = $form->getElement('base_fieldset');
        /* @var $baseFieldset \Magento\Data\Form\Element\Fieldset */

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
                'values'    => \Mage::getSingleton('Magento\Backend\Model\Config\Source\Yesno')->toOptionArray()
            ));

            if ($page->getPublishedRevisionId() && $page->getUnderVersionControl()) {
                $userId = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->getId();
                $accessLevel = \Mage::getSingleton('Magento\VersionsCms\Model\Config')->getAllowedAccessLevel();

                $revision = \Mage::getModel('Magento\VersionsCms\Model\Page\Revision')
                    ->loadWithRestrictions($accessLevel, $userId, $page->getPublishedRevisionId());

                if ($revision->getId()) {
                    $revisionNumber = $revision->getRevisionNumber();
                    $versionNumber = $revision->getVersionNumber();
                    $versionLabel = $revision->getLabel();

                    $page->setPublishedRevisionLink(
                        __('%1; rev #%2', $versionLabel, $revisionNumber));

                    $baseFieldset->addField('published_revision_link', 'link', array(
                            'label' => __('Currently Published Revision'),
                            'href' => \Mage::getSingleton('Magento\Backend\Model\Url')
                                ->getUrl('*/cms_page_revision/edit', array(
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

        /*
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    public function cmsControllerRouterMatchBefore(\Magento\Event\Observer $observer)
    {
        if (!$this->_cmsHierarchy->isEnabled()) {
            return $this;
        }

        $condition = $observer->getEvent()->getCondition();

        /**
         * Validate Request and modify router match condition
         */
        /* @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
        $node = \Mage::getModel('Magento\VersionsCms\Model\Hierarchy\Node', array('data' => array(
            'scope' => \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE,
            'scope_id' => \Mage::app()->getStore()->getId(),
        )))->getHeritage();
        $requestUrl = $condition->getIdentifier();
        $node->loadByRequestUrl($requestUrl);

        if ($node->checkIdentifier($requestUrl, \Mage::app()->getStore())) {
            $condition->setContinue(false);
            if (!$node->getId()) {
                $collection = $node->getNodesCollection();
                foreach ($collection as $item) {
                    if ($item->getPageIdentifier() == $requestUrl) {
                        $url = \Mage::getUrl('', array('_direct' => $item->getRequestUrl()));
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
            $child = \Mage::getModel('Magento\VersionsCms\Model\Hierarchy\Node', array('data' => array(
                'scope' => $node->getScope(),
                'scope_id' => $node->getScopeId(),
            )));
            $child->loadFirstChildByParent($node->getId());
            if (!$child->getId()) {
                return $this;
            }
            $url   = \Mage::getUrl('', array('_direct' => $child->getRequestUrl()));
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    public function cmsPageSaveAfter(\Magento\Event\Observer $observer)
    {
        /* @var $page \Magento\Cms\Model\Page */
        $page = $observer->getEvent()->getObject();

        // Create new initial version & revision if it
        // is a new page or version control was turned on for this page.
        if ($page->getIsNewPage() || ($page->getUnderVersionControl()
            && $page->dataHasChangedFor('under_version_control'))
        ) {
            $version = \Mage::getModel('Magento\VersionsCms\Model\Page\Version');

            $revisionInitialData = $page->getData();
            $revisionInitialData['copied_from_original'] = true;

            $version->setLabel($page->getTitle())
                ->setAccessLevel(\Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC)
                ->setPageId($page->getId())
                ->setUserId(\Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->getId())
                ->setInitialRevisionData($revisionInitialData)
                ->save();

            if ($page->getUnderVersionControl()) {
                $revision = $version->getLastRevision();

                if ($revision instanceof \Magento\VersionsCms\Model\Page\Revision) {
                    $revision->publish();
                }
            }
        }

        if (!$this->_cmsHierarchy->isEnabled()) {
            return $this;
        }

        // rebuild URL rewrites if page has changed for identifier
        if ($page->dataHasChangedFor('identifier')) {
            \Mage::getSingleton('Magento\VersionsCms\Model\Hierarchy\Node')->updateRewriteUrls($page);
        }

        /*
         * Appending page to selected nodes it will remove pages from other nodes
         * which are not specified in array. So should be called even array is empty!
         * Returns array of new ids for page nodes array( oldId => newId ).
         */
        \Mage::getSingleton('Magento\VersionsCms\Model\Hierarchy\Node')->appendPageToNodes($page, $page->getAppendToNodes());

        /*
         * Updating sort order for nodes in parent nodes which have current page as child
         */
        $resource = \Mage::getResourceSingleton('Magento\VersionsCms\Model\Resource\Hierarchy\Node');
        foreach ($page->getNodesSortOrder() as $nodeId => $value) {
            $resource->updateSortOrder($nodeId, $value);
        }

        return $this;
    }

    /**
     * Preparing cms page object before it will be saved
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    public function cmsPageSaveBefore(\Magento\Event\Observer $observer)
    {
        /* @var $page \Magento\Cms\Model\Page */
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
        } else if (!$page->getUnderVersionControl()) {
            $page->setPublishedRevisionId(null);
        }

        /*
         * Checking if node's data was passed and if yes. Saving new sort order for nodes.
         */
        $nodesData = $page->getNodesData();
        $appendToNodes = array();
        $sortOrder = array();
        if ($nodesData) {
            try{
                $nodesData = $this->_coreData->jsonDecode($page->getNodesData());
            } catch (\Zend_Json_Exception $e) {
                $nodesData=null;
            }
            if (!empty($nodesData)) {
                foreach ($nodesData as $row) {
                    if (isset($row['page_exists']) && $row['page_exists']) {
                        $appendToNodes[$row['node_id']] = 0;
                    }

                    if (isset($appendToNodes[$row['parent_node_id']])) {
                        if (strpos($row['node_id'], '_') !== FALSE) {
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    public function adminUserDeleteAfter(\Magento\Event\Observer $observer)
    {
        $version = \Mage::getModel('Magento\VersionsCms\Model\Page\Version');
        $collection = $version->getCollection()
            ->addAccessLevelFilter(\Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PRIVATE)
            ->addUserIdFilter();

         \Mage::getSingleton('Magento\Core\Model\Resource\Iterator')
            ->walk($collection->getSelect(), array(array($this, 'removeVersionCallback')), array('version'=> $version));

         return $this;
    }

    /**
     * Clean up hierarchy tree that belongs to website.
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    public function deleteWebsite(\Magento\Event\Observer $observer)
    {
        /* @var $store \Magento\Core\Model\Website */
        $website = $observer->getEvent()->getWebsite();
        $nodeModel = \Mage::getModel('Magento\VersionsCms\Model\Hierarchy\Node');
        $nodeModel->deleteByScope(\Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_WEBSITE, $website->getId());

        foreach ($website->getStoreIds() as $storeId) {
            $this->_cleanStoreFootprints($storeId);
        }

        return $this;
    }

    /**
     * Clean up hierarchy tree that belongs to store.
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    public function deleteStore(\Magento\Event\Observer $observer)
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
        $storeScope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE;
        $nodeModel = \Mage::getModel('Magento\VersionsCms\Model\Hierarchy\Node');
        $nodeModel->deleteByScope($storeScope, $storeId);

        /* @var $widgetModel \Magento\Widget\Model\Widget\Instance */
        $widgetModel = \Mage::getModel('Magento\Widget\Model\Widget\Instance');
        $widgets = $widgetModel->getResourceCollection()
                ->addStoreFilter(array($storeId, false))
                ->addFieldToFilter('instance_type', 'Magento\VersionsCms\Block\Widget\Node');

        /* @var $widgetInstance \Magento\Widget\Model\Widget\Instance */
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
        } catch (\Magento\Core\Exception $e) {
            // If we have situation when revision from
            // orphaned private version published we should
            // change its access level to protected so publisher
            // will have chance to see it and assign to some user
            $version->setAccessLevel(\Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PROTECTED);
            $version->save();
        }
    }

    /**
     * Removing unneeded data from increment table for removed page.
     *
     * @param $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    public function cmsPageDeleteAfter(\Magento\Event\Observer $observer)
    {
        /* @var $page \Magento\Cms\Model\Page */
        $page = $observer->getEvent()->getObject();

        \Mage::getResourceSingleton('Magento\VersionsCms\Model\Resource\Increment')
            ->cleanIncrementRecord(\Magento\VersionsCms\Model\Increment::TYPE_PAGE,
                $page->getId(),
                \Magento\VersionsCms\Model\Increment::LEVEL_VERSION);

        return $this;
    }

    /**
     * Handler for cms hierarchy view
     *
     * @param array $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event|false
     */
    public function postDispatchCmsHierachyView($config, $eventModel)
    {
        return $eventModel->setInfo(__('Tree Viewed'));
    }

    /**
     * Handler for cms revision preview
     *
     * @param array $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event|false
     */
    public function postDispatchCmsRevisionPreview($config, $eventModel)
    {
        return $eventModel->setInfo(\Mage::app()->getRequest()->getParam('revision_id'));
    }

    /**
     * Handler for cms revision publish
     *
     * @param array $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event|false
     */
    public function postDispatchCmsRevisionPublish($config, $eventModel)
    {
        return $eventModel->setInfo(\Mage::app()->getRequest()->getParam('revision_id'));
    }

    /**
     * Add Hierarchy Menu layout handle to Cms page rendering
     *
     * @param $observer
     * @return \Magento\VersionsCms\Model\Observer
     */
    public function affectCmsPageRender(\Magento\Event\Observer $observer)
    {
        if (!is_object($this->_coreRegistry->registry('current_cms_hierarchy_node'))
            || !$this->_cmsHierarchy->isEnabled()
        ) {
            return $this;
        }

        /* @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
        $node = $this->_coreRegistry->registry('current_cms_hierarchy_node');

        /* @var $action \Magento\Core\Controller\Varien\Action */
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
     * @param \Magento\Event\Observer $observer
     */
    public function addCmsToTopmenuItems(\Magento\Event\Observer $observer)
    {
        /**
         * @var $topMenuRootNode \Magento\Data\Tree\Node
         */
        $topMenuRootNode = $observer->getMenu();

        $hierarchyModel = \Mage::getModel('Magento\VersionsCms\Model\Hierarchy\Node', array('data' => array(
            'scope' => \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE,
            'scope_id' => \Mage::app()->getStore()->getId(),
        )))->getHeritage();

        $nodes = $hierarchyModel->getNodesData();
        $tree = $topMenuRootNode->getTree();

        $nodesFlatList = array(
            $topMenuRootNode->getId() => $topMenuRootNode
        );

        $nodeModel = \Mage::getModel('Magento\VersionsCms\Model\Hierarchy\Node');

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

            $menuNode = new \Magento\Data\Tree\Node($menuNodeData, 'id', $tree, $parentNode);
            $parentNode->addChild($menuNode);

            $nodesFlatList[$menuNodeId] = $menuNode;
        }
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
