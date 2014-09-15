<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Backend;

use Magento\Framework\Event\Observer as EventObserver;

/**
 * Versions cms page observer for backend area
 */
class Observer
{
    /**
     * Configuration model
     *
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Cms hierarchy
     *
     * @var \Magento\VersionsCms\Helper\Hierarchy
     */
    protected $_cmsHierarchy;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\Config\Source\Yesno
     */
    protected $_sourceYesno;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\VersionsCms\Model\Page\RevisionFactory
     */
    protected $_revisionFactory;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory
     */
    protected $_hierarchyNodeFactory;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\Node
     */
    protected $_hierarchyNode;

    /**
     * @var \Magento\VersionsCms\Model\Page\VersionFactory
     */
    protected $_pageVersionFactory;

    /**
     * @var \Magento\VersionsCms\Model\Resource\Page\Version\CollectionFactory
     */
    protected $_versionCollectionFactory;

    /**
     * @var \Magento\Framework\Model\Resource\Iterator
     */
    protected $_resourceIterator;

    /**
     * @var \Magento\Widget\Model\Resource\Widget\Instance\CollectionFactory
     */
    protected $_widgetCollectionFactory;

    /**
     * @var \Magento\VersionsCms\Model\Resource\Hierarchy\Node
     */
    protected $_hierarchyNodeResource;

    /**
     * @var \Magento\VersionsCms\Model\Resource\Increment
     */
    protected $_cmsIncrement;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\VersionsCms\Model\Config $config
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Backend\Model\Config\Source\Yesno $sourceYesno
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\VersionsCms\Model\Page\RevisionFactory $revisionFactory
     * @param \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory
     * @param \Magento\VersionsCms\Model\Hierarchy\Node $hierarchyNode
     * @param \Magento\VersionsCms\Model\Page\VersionFactory $pageVersionFactory
     * @param \Magento\VersionsCms\Model\Resource\Page\Version\CollectionFactory $versionCollectionFactory
     * @param \Magento\Framework\Model\Resource\Iterator $resourceIterator
     * @param \Magento\Widget\Model\Resource\Widget\Instance\CollectionFactory $widgetCollectionFactory
     * @param \Magento\VersionsCms\Model\Resource\Hierarchy\Node $hierarchyNodeResource
     * @param \Magento\VersionsCms\Model\Resource\Increment $cmsIncrement
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\VersionsCms\Model\Config $config,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Config\Source\Yesno $sourceYesno,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\VersionsCms\Model\Page\RevisionFactory $revisionFactory,
        \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory,
        \Magento\VersionsCms\Model\Hierarchy\Node $hierarchyNode,
        \Magento\VersionsCms\Model\Page\VersionFactory $pageVersionFactory,
        \Magento\VersionsCms\Model\Resource\Page\Version\CollectionFactory $versionCollectionFactory,
        \Magento\Framework\Model\Resource\Iterator $resourceIterator,
        \Magento\Widget\Model\Resource\Widget\Instance\CollectionFactory $widgetCollectionFactory,
        \Magento\VersionsCms\Model\Resource\Hierarchy\Node $hierarchyNodeResource,
        \Magento\VersionsCms\Model\Resource\Increment $cmsIncrement
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_coreData = $coreData;
        $this->_cmsHierarchy = $cmsHierarchy;
        $this->_config = $config;
        $this->_authorization = $authorization;
        $this->_sourceYesno = $sourceYesno;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_backendUrl = $backendUrl;
        $this->_revisionFactory = $revisionFactory;
        $this->_hierarchyNodeFactory = $hierarchyNodeFactory;
        $this->_hierarchyNode = $hierarchyNode;
        $this->_pageVersionFactory = $pageVersionFactory;
        $this->_versionCollectionFactory = $versionCollectionFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->_widgetCollectionFactory = $widgetCollectionFactory;
        $this->_hierarchyNodeResource = $hierarchyNodeResource;
        $this->_cmsIncrement = $cmsIncrement;
    }

    /**
     * Making changes to main tab regarding to custom logic
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function onMainTabPrepareForm($observer)
    {
        $form = $observer->getEvent()->getForm();
        /* @var $baseFieldset \Magento\Framework\Data\Form\Element\Fieldset */
        $baseFieldset = $form->getElement('base_fieldset');
        /* @var $baseFieldset \Magento\Framework\Data\Form\Element\Fieldset */

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
        /* @var $page \Magento\VersionsCms\Model\Page */
        $page = $this->_coreRegistry->registry('cms_page');
        $revisionAvailable = false;
        if ($page) {

            $baseFieldset->addField(
                'under_version_control',
                'select',
                array(
                    'label' => __('Under Version Control'),
                    'title' => __('Under Version Control'),
                    'name' => 'under_version_control',
                    'values' => $this->_sourceYesno->toOptionArray()
                )
            );

            if ($page->getPublishedRevisionId() && $page->getUnderVersionControl()) {
                $userId = $this->_backendAuthSession->getUser()->getId();
                $accessLevel = $this->_config->getAllowedAccessLevel();

                /** @var \Magento\VersionsCms\Model\Page\Revision $revision */
                $revision = $this->_revisionFactory->create()->loadWithRestrictions(
                    $accessLevel,
                    $userId,
                    $page->getPublishedRevisionId()
                );

                if ($revision->getId()) {
                    $revisionNumber = $revision->getRevisionNumber();
                    $versionLabel = $revision->getLabel();

                    $page->setPublishedRevisionLink(__('%1; rev #%2', $versionLabel, $revisionNumber));

                    $baseFieldset->addField(
                        'published_revision_link',
                        'link',
                        array(
                            'label' => __('Currently Published Revision'),
                            'href' => $this->_backendUrl->getUrl(
                                'adminhtml/cms_page_revision/edit',
                                array('page_id' => $page->getId(), 'revision_id' => $page->getPublishedRevisionId())
                            )
                        )
                    );

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
     * Processing extra data after cms page saved
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function cmsPageSaveAfter(EventObserver $observer)
    {
        /* @var $page \Magento\Cms\Model\Page */
        $page = $observer->getEvent()->getObject();

        // Create new initial version & revision if it
        // is a new page or version control was turned on for this page.
        if ($page->getIsNewPage() || $page->getUnderVersionControl() && $page->dataHasChangedFor(
            'under_version_control'
        )
        ) {
            /** @var \Magento\VersionsCms\Model\Page\Version $version */
            $version = $this->_pageVersionFactory->create();

            $revisionInitialData = $page->getData();
            $revisionInitialData['copied_from_original'] = true;

            $version->setLabel(
                $page->getTitle()
            )->setAccessLevel(
                \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC
            )->setPageId(
                $page->getId()
            )->setUserId(
                $this->_backendAuthSession->getUser()->getId()
            )->setInitialRevisionData(
                $revisionInitialData
            )->save();

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
     * @param EventObserver $observer
     * @return $this
     */
    public function cmsPageSaveBefore(EventObserver $observer)
    {
        /* @var \Magento\Cms\Model\Page $page */
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
            } catch (\Zend_Json_Exception $e) {
                $nodesData = null;
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
     * @return $this
     */
    public function adminUserDeleteAfter()
    {
        /** @var \Magento\VersionsCms\Model\Resource\Page\Version\Collection $collection */
        $collection = $this->_versionCollectionFactory->create()->addAccessLevelFilter(
            \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PRIVATE
        )->addUserIdFilter();

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
     * @param EventObserver $observer
     * @return $this
     */
    public function deleteWebsite(EventObserver $observer)
    {
        /* @var $store \Magento\Store\Model\Website */
        $website = $observer->getEvent()->getWebsite();

        $this->_hierarchyNodeFactory->create()->deleteByScope(
            \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_WEBSITE,
            $website->getId()
        );

        foreach ($website->getStoreIds() as $storeId) {
            $this->_cleanStoreFootprints($storeId);
        }

        return $this;
    }

    /**
     * Clean up hierarchy tree that belongs to store.
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function deleteStore(EventObserver $observer)
    {
        $storeId = $observer->getEvent()->getStore()->getId();
        $this->_cleanStoreFootprints($storeId);
        return $this;
    }

    /**
     * Clean up information about deleted store from the widgets and hierarchy nodes
     *
     * @param int $storeId
     * @return void
     */
    private function _cleanStoreFootprints($storeId)
    {
        $storeScope = \Magento\VersionsCms\Model\Hierarchy\Node::NODE_SCOPE_STORE;
        $this->_hierarchyNodeFactory->create()->deleteByScope($storeScope, $storeId);

        /** @var \Magento\Widget\Model\Resource\Widget\Instance\Collection $widgets */
        $widgets = $this->_widgetCollectionFactory->create()->addStoreFilter(
            array($storeId, false)
        )->addFieldToFilter(
            'instance_type',
            'Magento\VersionsCms\Block\Widget\Node'
        );

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
     * @return void
     */
    public function removeVersionCallback($args)
    {
        $version = $args['version'];
        $version->setData($args['row']);

        try {
            $version->delete();
        } catch (\Magento\Framework\Model\Exception $e) {
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
     * @param EventObserver $observer
     * @return $this
     */
    public function cmsPageDeleteAfter(EventObserver $observer)
    {
        /* @var $page \Magento\Cms\Model\Page */
        $page = $observer->getEvent()->getObject();

        $this->_cmsIncrement->cleanIncrementRecord(
            \Magento\VersionsCms\Model\Increment::TYPE_PAGE,
            $page->getId(),
            \Magento\VersionsCms\Model\Increment::LEVEL_VERSION
        );

        return $this;
    }
}
