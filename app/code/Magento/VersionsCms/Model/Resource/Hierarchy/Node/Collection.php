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
 * Cms Page Hierarchy Tree Nodes Collection
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\VersionsCms\Model\Resource\Hierarchy\Node;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define resource model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\VersionsCms\Model\Hierarchy\Node', 'Magento\VersionsCms\Model\Resource\Hierarchy\Node');
    }

    /**
     * Join Cms Page data to collection
     *
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function joinCmsPage()
    {
        if (!$this->getFlag('cms_page_data_joined')) {
            $this->getSelect()->joinLeft(
                array('page_table' => $this->getTable('cms_page')),
                'main_table.page_id = page_table.page_id',
                array(
                    'page_title'        => 'title',
                    'page_identifier'   => 'identifier'
                )
            );
            $this->setFlag('cms_page_data_joined', true);
        }
        return $this;
    }

    /**
     * Add Store Filter to assigned CMS pages
     *
     * @param int|\Magento\Core\Model\Store $store
     * @param bool $withAdmin Include admin store or not
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Core\Model\Store) {
            $store = $store->getId();
        }

        if ($withAdmin) {
            $storeIds = array(\Magento\Core\Model\AppInterface::ADMIN_STORE_ID, $store);
        } else {
            $storeIds = array($store);
        }

        $this->addCmsPageInStoresColumn();
        $this->getFlag('page_in_stores_select')
            ->where('store.store_id IN (?)', $storeIds);
        $this->getSelect()
            ->having('main_table.page_id IS NULL OR page_in_stores IS NOT NULL');
        return $this;
    }

    /**
     * Adding sub query for custom column to determine on which stores page active.
     *
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function addCmsPageInStoresColumn()
    {
        if (!$this->getFlag('cms_page_in_stores_data_joined')) {
            $subSelect = $this->getConnection()->select();
            $subSelect->from(array('store' => $this->getTable('cms_page_store')), array())
                ->where('store.page_id = main_table.page_id');
            $subSelect = \Mage::getResourceHelper('Magento_Core')->addGroupConcatColumn($subSelect, 'store_id', 'store_id');
            $this->getSelect()->columns(array('page_in_stores' => new \Zend_Db_Expr('(' . $subSelect . ')')));

            // save subSelect to use later
            $this->setFlag('page_in_stores_select', $subSelect);

            $this->setFlag('cms_page_in_stores_data_joined', true);
        }
        return $this;
    }

    /**
     * Order nodes as tree
     *
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function setTreeOrder()
    {
        if (!$this->getFlag('tree_order_added')) {
            $this->getSelect()->order(array(
                'parent_node_id', 'level', 'main_table.sort_order'
            ));
            $this->setFlag('tree_order_added', true);
        }
        return $this;
    }

    /**
     * Order tree by level and position
     *
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function setOrderByLevel()
    {
        $this->getSelect()->order(array('main_table.level','main_table.sort_order'));
        return $this;
    }

    /**
     * Join meta data for tree root nodes from extra table.
     *
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function joinMetaData()
    {
        if (!$this->getFlag('meta_data_joined')) {
            $this->getSelect()
                ->joinLeft(array('metadata_table' => $this->getTable('magento_versionscms_hierarchy_metadata')),
                    'main_table.node_id = metadata_table.node_id',
                    array(
                        'meta_first_last',
                        'meta_next_previous',
                        'meta_chapter',
                        'meta_section',
                        'meta_cs_enabled',
                        'pager_visibility',
                        'pager_frame',
                        'pager_jump',
                        'menu_visibility',
                        'menu_layout',
                        'menu_brief',
                        'menu_excluded',
                        'menu_levels_down',
                        'menu_ordered',
                        'menu_list_type',
                        'top_menu_visibility',
                        'top_menu_excluded'
                    ));
        }
        $this->setFlag('meta_data_joined', true);
        return $this;
    }

    /**
     * Join main table on self to discover which nodes
     * have defined page as direct child node.
     *
     * @param int|\Magento\Cms\Model\Page $page
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function joinPageExistsNodeInfo($page)
    {
        if (!$this->getFlag('page_exists_joined')) {
            if ($page instanceof \Magento\Cms\Model\Page) {
                $page = $page->getId();
            }

            $connection = $this->getConnection();

            $onClause = 'main_table.node_id = clone.parent_node_id AND clone.page_id = ?';
            $ifPageExistExpr = $connection->getCheckSql('clone.node_id IS NULL', '0', '1');
            $ifCurrentPageExpr = $connection->quoteInto(
                $connection->getCheckSql('main_table.page_id = ?', '1', '0'),
                $page);
            $this->getSelect()->joinLeft(
                    array('clone' => $this->getResource()->getMainTable()),
                    $connection->quoteInto($onClause, $page),
                    array('page_exists' => $ifPageExistExpr, 'current_page' => $ifCurrentPageExpr)
                );

            $this->setFlag('page_exists_joined', true);
        }
        return $this;
    }

    /**
     * Apply filter to retrieve nodes with ids which
     * were defined as parameter or nodes which contain
     * defined page in their direct children.
     *
     * @param int|array $nodeIds
     * @param int|\Magento\Cms\Model\Page|null $page
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function applyPageExistsOrNodeIdFilter($nodeIds, $page = null)
    {
        if (!$this->getFlag('page_exists_or_node_id_filter_applied')) {
            if (!$this->getFlag('page_exists_joined')) {
                $this->joinPageExistsNodeInfo($page);
            }
            if (count($nodeIds) == 0) {
                $nodeIds = 0;
            }

            $this->getSelect()->where('clone.node_id IS NOT NULL OR main_table.node_id IN (?)', $nodeIds);
            $this->setFlag('page_exists_or_node_id_filter_applied', true);
        }

        return $this;
    }

    /**
     * Adds dynamic column with maximum value (which means that it
     * is sort_order of last direct child) of sort_order column in scope of one node.
     *
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function addLastChildSortOrderColumn()
    {
        if (!$this->getFlag('last_child_sort_order_column_added')) {
            $subSelect = $this->getConnection()->select();
            $subSelect->from($this->getResource()->getMainTable(), new \Zend_Db_Expr('MAX(sort_order)'))
                ->where('parent_node_id = main_table.node_id');
            $this->getSelect()->columns(array('last_child_sort_order' => $subSelect));
            $this->setFlag('last_child_sort_order_column_added', true);
        }

        return $this;
    }

    /**
     * Apply filter to retrieve only root nodes.
     *
     * @return \Magento\VersionsCms\Model\Resource\Hierarchy\Node\Collection
     */
    public function applyRootNodeFilter()
    {
        $this->addFieldToFilter('parent_node_id', array('null' => true));
        return $this;
    }

    /**
     * Apply filter to retrieve only proper scope nodes.
     *
     * @param string $scope Scope name: default|store|website
     */
    public function applyScope($scope)
    {
        $this->getSelect()->where('main_table.scope = ?', $scope);
        return $this;
    }

    /**
     * Apply filter to retrieve only proper scope ID nodes.
     *
     * @param int $codeId
     */
    public function applyScopeId($codeId)
    {
        $this->getSelect()->where('main_table.scope_id = ?', $codeId);
        return $this;
    }
}
