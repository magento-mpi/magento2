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
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms Hierarchy Pages Node Resource Model
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Mysql4_Hierarchy_Node extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Primary key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Secondary table for storing meta data
     * @var string
     */
    protected $_metadataTable;

    /**
     * Initialize connection and define main table and field
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/hierarchy_node', 'node_id');
        $this->_metadataTable = $this->getTable('enterprise_cms/hierarchy_metadata');
    }

    /**
     * Retrieve select object for load object data.
     * Join page information if page assigned.
     * Join secondary table with meta data for root nodes.
     *
     * @param string $field
     * @param mixed $value
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinLeft(array('page_table' => $this->getTable('cms/page')),
                $this->getMainTable() . '.page_id = page_table.page_id',
                array(
                    'page_title'        => 'title',
                    'page_identifier'   => 'identifier',
                    'page_is_active'    => 'is_active'
                ))
            ->joinLeft(array('metadata_table' => $this->_metadataTable),
                $this->getMainTable() . '.' . $this->getIdFieldName() . ' = metadata_table.node_id',
                array(
                    'meta_first_last',
                    'meta_next_previous',
                    'meta_chapter',
                    'meta_section',
                    'pager_visibility',
                    'pager_frame',
                    'pager_jump',
                    'menu_visibility',
                    'menu_levels_up',
                    'menu_levels_down',
                    'menu_ordered',
                    'menu_list_type'
                ));

        return $select;
    }

    /**
     * Load node by Request Path
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @param string $url
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function loadByRequestUrl($object, $url)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($url)) {
            $select = $this->_getLoadSelect('request_url', $url, $object);
            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);
        return $this;
    }

    /**
     * Load First node by parent node id
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @param int $parentNodeId
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function loadFirstChildByParent($object, $parentNodeId)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($parentNodeId)) {
            $select = $this->_getLoadSelect('parent_node_id', $parentNodeId, $object)
                ->order(array($this->getMainTable().'.sort_order'))
                ->limit(1);
            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);
        return $this;
    }

    /**
     * Remove children by root node.
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function removeTreeChilds($object)
    {
        $where = $this->_getWriteAdapter()->quoteInto('parent_node_id=?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }

    /**
     * Retrieve xpaths array which contains defined page
     *
     * @param int $pageId
     * @return array
     */
    public function getTreeXpathsByPage($pageId)
    {
        $treeXpaths = array();
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'xpath')
            ->where('page_id=?', $pageId);

        $rowset = $this->_getReadAdapter()->fetchAll($select);
        $treeXpaths = array();
        foreach ($rowset as $row) {
            $treeXpaths[] = $row['xpath'];
        }
        return $treeXpaths;
    }

    /**
     * Rebuild URL rewrites for a tree with specified path.
     *
     * @param string $xpath
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function updateRequestUrlsForTreeByXpath($xpath)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(
                array('node_table' => $this->getMainTable()),
                array($this->getIdFieldName(), 'parent_node_id', 'page_id', 'identifier', 'request_url'))
            ->joinLeft(
                array('page_table' => $this->getTable('cms/page')),
                'node_table.page_id=page_table.page_id',
                array(
                    'page_identifier' => 'identifier',
                ))
            ->where('xpath LIKE ?', $xpath. '/%')
            ->orWhere('xpath = ?', $xpath)
            ->group('node_table.node_id')
            ->order(array('level', 'node_table.sort_order'));

        $nodes      = array();
        $rowSet     = $select->query()->fetchAll();
        foreach ($rowSet as $row) {
            $nodes[intval($row['parent_node_id'])][$row[$this->getIdFieldName()]] = $row;
        }

        if (!$nodes) {
            return $this;
        }

        $keys = array_keys($nodes);
        $parentNodeId = array_shift($keys);
        $this->_updateNodeRequestUrls($nodes, $parentNodeId, null);

        return $this;
    }

    /**
     * Recursive update Request URL for node and all it's children
     *
     * @param array $nodes
     * @param int $parentNodeId
     * @param string $path
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    protected function _updateNodeRequestUrls(array $nodes, $parentNodeId = 0, $path = null)
    {
        foreach ($nodes[$parentNodeId] as $nodeRow) {
            $identifier = $nodeRow['page_id'] ? $nodeRow['page_identifier'] : $nodeRow['identifier'];

            if ($path) {
                $requestUrl = $path . '/' . $identifier;
            } else {
                $route = explode('/', $nodeRow['request_url']);
                array_pop($route);
                $route[] = $identifier;
                $requestUrl = implode('/', $route);
            }

            if ($nodeRow['request_url'] != $requestUrl) {
                $this->_getWriteAdapter()->update($this->getMainTable(), array(
                    'request_url' => $requestUrl
                ), $this->_getWriteAdapter()->quoteInto($this->getIdFieldName().'=?', $nodeRow[$this->getIdFieldName()]));
            }

            if (isset($nodes[$nodeRow[$this->getIdFieldName()]])) {
                $this->_updateNodeRequestUrls($nodes, $nodeRow[$this->getIdFieldName()], $requestUrl);
            }
        }

        return $this;
    }

    /**
     * Check identifier
     *
     * If a CMS Page belongs to a tree (binded to a tree node), it should not be accessed standalone
     * only by URL that identifies it in a hierarchy.
     *
     * @param string $identifier
     * @return bool
     */
    public function checkIdentifier($identifier)
    {
        $select = $this->getReadConnection()->select()
            ->from(array('page_table' => $this->getTable('cms/page')), 'COUNT(page_table.page_id)')
            ->join(
                array('node_table' => $this->getMainTable()),
                'page_table.page_id = node_table.page_id',
                array())
            ->where('page_table.identifier=?', $identifier)
            ->where('page_table.website_root <> 1');
        return $this->_getReadAdapter()->fetchOne($select) > 0;
    }

    /**
     * Prepare xpath after object save
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->dataHasChangedFor($this->getIdFieldName())) {
            // update xpath
            $xpath = $object->getXpath() . $object->getId();
            $bind = array('xpath' => $xpath);
            $where = $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . '=?', $object->getId());
            $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);
            $object->setXpath($xpath);
        }

        return $this;
    }

    /**
     * Saving meta if such available for node (in case node is root node of three)
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function saveMetaData(Mage_Core_Model_Abstract $object)
    {
        // we save to metadata table not only metadata :(
        //if ($object->getParentNodeId()) {
        //    return $this;
        //}
        $preparedData = $this->_prepareDataForTable($object, $this->_metadataTable);
        $this->_getWriteAdapter()->insertOnDuplicate(
            $this->_metadataTable, $preparedData, array_keys($preparedData));
        return $this;
    }

    /**
     * Load meta node's data by Parent node and Type
     * Allowed types:
     *  - chapter       parent node chapter
     *  - section       parent node section
     *  - first         first node in current parent node level
     *  - last          last node in current parent node level
     *  - next          next node (only in current parent node level)
     *  - previous      previous node (only in current parent node level)
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $node
     * @param Enterprise_Cms_Model_Hierarchy_Node $node The parent node
     * @param string $type
     * @return Enterprise_Cms_Model_Hierarchy_Node
     */
    public function getMetaNodeDataByType($node, $type)
    {
        if (!$node->getParentNodeId()) {
            return false;
        }
        $read = $this->_getReadAdapter();
        if ($read) {
            $select = $this->_getLoadSelectWithoutWhere();
            $found  = false;
            switch ($type) {
// commented bc of changes in road map
//                case 'chapter':
//                    $xpath = split('/', $node->getXpath());
//                    if (isset($xpath[1]) && $xpath[1] != $node->getId()) {
//                        $found = true;
//                        $select->where($this->getMainTable() . '.' . $this->getIdFieldName() . '=?', $xpath[1]);
//                    }
//                    break;
//
//                case 'section':
//                    $xpath = split('/', $node->getXpath());
//                    if (isset($xpath[2]) && $xpath[2] != $node->getId()) {
//                        $found = true;
//                        $select->where($this->getMainTable() . '.' . $this->getIdFieldName() . '=?', $xpath[2]);
//                    }
//                    break;

                case 'first':
                    $found = true;
                    $select->where($this->getMainTable() . '.parent_node_id=?', $node->getParentNodeId());
                    $select->order($this->getMainTable() . '.sort_order ASC');
                    $select->limit(1);
                    break;

                case 'last':
                    $found = true;
                    $select->where($this->getMainTable() . '.parent_node_id=?', $node->getParentNodeId());
                    $select->order($this->getMainTable() . '.sort_order DESC');
                    $select->limit(1);
                    break;

                case 'previous':
                    if ($node->getSortOrder() > 0) {
                        $found = true;
                        $select->where($this->getMainTable() . '.parent_node_id=?', $node->getParentNodeId());
                        $select->where($this->getMainTable() . '.sort_order<?', $node->getSortOrder());
                        $select->order($this->getMainTable() . '.sort_order DESC');
                        $select->limit(1);
                    }
                    break;

                case 'next':
                    $found = true;
                    $select->where($this->getMainTable() . '.parent_node_id=?', $node->getParentNodeId());
                    $select->where($this->getMainTable() . '.sort_order>?', $node->getSortOrder());
                    $select->order($this->getMainTable() . '.sort_order ASC');
                    $select->limit(1);
                    break;
            }

            if (!$found) {
                return false;
            }

            return $read->fetchRow($select);
        }

        return false;
    }

    /**
     * Retrieve Tree Slice
     * 2 level array
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @param int $up
     * @param int $down
     * @return array
     */
    public function getTreeSlice($object, $up = 0, $down = 0)
    {
        $tree       = array();
        $parentId   = $object->getParentNodeId();
        if ($up > 0 && $object->getLevel() > 1) {
            $xpath = split('/', $object->getXpath());
            array_pop($xpath); //remove self node
            array_pop($xpath); //remove parent node
            $parentIds = array();
            while (count($xpath) > 0) {
                if ($up == 0) {
                    break;
                }
                $parentIds[] = array_pop($xpath);
                $up --;
            }

            if ($parentIds) {
                $parentId = $parentIds[count($parentIds) -1];
                $select = $this->_getLoadSelectWithoutWhere()
                    ->where('parent_node_id IN (?)', $parentIds)
                    ->order(array('level', $this->getMainTable().'.sort_order'));
                $nodes = $select->query()->fetchAll();
                $tree = $this->_prepareRelatedStructure($nodes, $parentId, $tree);
            }
        }

        if ($object->getParentNodeId() === null) {
            $where = 'parent_node_id IS NULL';
        } else {
            $where = $this->_getReadAdapter()->quoteInto('parent_node_id=?', $object->getParentNodeId());
        }
        if ($down > 0) {
            $xpath = $object->getXpath() . '/%';
            $level = $object->getLevel() + $down + 1;
            $where .= ' OR (' . $this->_getReadAdapter()->quoteInto('xpath LIKE ?', $xpath)
                . ' AND ' . $this->_getReadAdapter()->quoteInto('level < ?', $level) . ')';
        }

        $select = $this->_getLoadSelectWithoutWhere()
            ->where($where)
            ->order(array('level', $this->getMainTable().'.sort_order'));

        $nodes = $select->query()->fetchAll();
        $tree = $this->_prepareRelatedStructure($nodes, $parentId, $tree);

        return $tree;
    }

    /**
     * Preparing array where all nodes grouped in sub arrays by parent id.
     *
     * @param array $nodes source node's data
     * @param int $startNodeId
     * @param array $tree Initial array which will modified and returned with new data
     * @return array
     */
    protected function _prepareRelatedStructure($nodes, $startNodeId, $tree)
    {
        foreach ($nodes as $row) {
            $parentNodeId = $row['parent_node_id'] == $startNodeId ? 0 : $row['parent_node_id'];
            $tree[$parentNodeId][$row[$this->getIdFieldName()]] = $row;
        }

        return $tree;
    }

    /**
     * Retrieve Parent node children
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @return array
     */
    public function getParentNodeChildren($object)
    {
        $children = array();
        if ($object->getParentNodeId() === null) {
            $where = 'parent_node_id IS NULL';
        } else {
            $where = $this->_getReadAdapter()->quoteInto('parent_node_id=?', $object->getParentNodeId());
        }
        $select = $this->_getLoadSelectWithoutWhere()
            ->where($where)
            ->order($this->getMainTable().'.sort_order');
        $nodes = $select->query()->fetchAll();

        return $nodes;
    }

    /**
     * Return nearest parent params for pagination/menu or self object params if it doesn't inherit settings
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @param string $visibilityFieldName Visibility field name from metadata table
     * @param bool $onlyParent Use only parent nodes settings, ignoring object params
     * @return array|null
     */
    public function getMetadataParamsBasedOnVisibility($object, $visibilityFieldName, $onlyParent = false)
    {
        $params = array();

        $objectFlag = $object->getData($visibilityFieldName);
        if ($objectFlag == Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_PARENT || $onlyParent) {
            $parentIds = preg_split('/\/{1}/', $object->getXpath(), 0, PREG_SPLIT_NO_EMPTY);
            array_pop($parentIds); //remove self node
            $select = $this->_getLoadSelectWithoutWhere()
                ->where($this->getMainTable().'.node_id IN (?)', $parentIds)
                ->where('metadata_table.'.$visibilityFieldName.' IN (?)', array(
                    Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_YES,
                    Enterprise_Cms_Helper_Hierarchy::METADATA_VISIBILITY_NO
                ))
                ->order(array($this->getMainTable().'.level DESC'))
                ->limit(1);
            $params = $this->_getReadAdapter()->fetchRow($select);
        } else {
            $params = $object->getData();
        }
        if (is_array($params) && count($params) > 0) {
            return $params;
        }
        return null;
    }

    /**
     * Load page data for model if defined page id
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function loadPageData($object)
    {
        $pageId = $object->getPageId();
        if (!empty($pageId)) {
            $columns = array(
                'page_title'        => 'title',
                'page_identifier'   => 'identifier',
                'page_is_active'    => 'is_active'
            );
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('cms/page'), $columns)
                ->where('page_id=?', $pageId)
                ->limit(1);
            $row = $this->_getReadAdapter()->fetchRow($select);
            if ($row) {
                $object->addData($row);
            }
        }
        return $this;
    }

    /**
     * Remove node which are representing specified page from defined nodes.
     * Which will also remove child nodes by foreign key.
     *
     * @param int $pageId
     * @param int|array $nodes
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function removePageFromNodes($pageId, $nodes)
    {
        $write = $this->_getWriteAdapter();
        $whereClause = $write->quoteInto('page_id = ? AND ', $pageId);
        $whereClause .= $write->quoteInto('parent_node_id IN (?)', $nodes);
        $write->delete($this->getMainTable(), $whereClause);

        return $this;
    }

    /**
     * Remove nodes defined by id.
     * Which will also remove their child nodes by foreign key.
     *
     * @param int|array $nodeIds
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function dropNodes($nodeIds)
    {
        $write = $this->_getWriteAdapter();
        $whereClause = $write->quoteInto('node_id IN (?)', $nodeIds);
        $write->delete($this->getMainTable(), $whereClause);

        return $this;
    }

    /**
     * Retrieve tree meta data flags from secondary table.
     * Filtering by root node of passed node.
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @return array|bool
     */
    public function getTreeMetaData(Enterprise_Cms_Model_Hierarchy_Node $object) {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $xpath = explode('/', $object->getXpath());
        $select->from($this->_metadataTable)
            ->where('node_id = ?', $xpath[0]);

        return $read->fetchRow($select);
    }

    /**
     * Prepare load select but without where part.
     * So all extra joins to secondary tables will be present.
     *
     * @return Zend_Db_Select
     */
    public function _getLoadSelectWithoutWhere()
    {
        return $this->_getLoadSelect(null, null, null)
            ->reset(Zend_Db_Select::WHERE);
    }

    /**
     * Updating nodes sort_order with new value.
     *
     * @param int $nodeId
     * @param int $sortOrder
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function updateSortOrder($nodeId, $sortOrder)
    {
        $this->_getWriteAdapter()->update($this->getMainTable(),
                array('sort_order' => $sortOrder),
                array($this->getIdFieldName() . ' = ? ' => $nodeId));

        return $this;
    }
}
