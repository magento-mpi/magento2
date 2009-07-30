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
 * Cms Hieararchy Pages Node Resource Model
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
     * Initialize connection and define main table and field
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/hierarchy_node', 'node_id');
    }

    /**
     * Retrieve select object for load object data
     * Join page information if page assigned
     *
     * @param string $field
     * @param mixed $value
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinLeft(
            array('page_table' => $this->getTable('cms/page')),
            $this->getMainTable() . '.page_id = page_table.page_id',
            array(
                'page_title'        => 'title',
                'page_identifier'   => 'identifier',
                'page_is_active'    => 'is_active'
            )
        );
        return $select;
    }

    /**
     * Load Parent Node by Hierarchy Page Tree ID
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @param int $treeId
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function loadByHierarchy($object, $treeId)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($treeId)) {
            $select = $this->_getLoadSelect('tree_id', $treeId, $object);
            $select->where('parent_node_id IS NULL');
            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);
        return $this;
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
                ->order(array('sort_order'))
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
     * Validate Unique Hierarchy Identifier
     *
     * @param string $identifier
     * @param int $treeId
     * @return bool
     */
    public function validateHierarchyIdentifier($identifier, $treeId = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('parent_node_id IS NULL')
            ->where('identifier=?', $identifier);
        if ($treeId) {
            $select->where('tree_id!=?', $treeId);
        }

        if ($this->_getReadAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * Remove children by root
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
     * Retrieve tree ids array where the page is
     *
     * @param int $pageId
     * @return array
     */
    public function getTreeIdsByPage($pageId)
    {
        $treeIds = array();
        $select = $this->_getReadAdapter()->select()
            ->distinct(true)
            ->from($this->getMainTable(), 'tree_id')
            ->where('page_id=?', $pageId);
        $rowset = $this->_getReadAdapter()->fetchAll($select);
        $treeIds = array();
        foreach ($rowset as $row) {
            $treeIds[$row['tree_id']] = $row['tree_id'];
        }
        return $treeIds;
    }

    /**
     * Rebuild URL rewrites for a tree
     *
     * @param int $treeId
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    public function updateRequestUrlsForTree($treeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_cms/hierarchy'))
            ->where('tree_id=?', $treeId);
        $treeRow = $this->_getReadAdapter()->fetchRow($select);
        if (!$treeRow) {
            return $this;
        }
        $select = $this->_getReadAdapter()->select()
            ->from(
                array('node_table' => $this->getMainTable()),
                array('node_id', 'parent_node_id', 'page_id', 'identifier', 'request_url'))
            ->joinLeft(
                array('page_table' => $this->getTable('cms/page')),
                'node_table.page_id=page_table.page_id',
                array(
                    'page_identifier' => 'identifier',
                ))
            ->where('tree_id=?', $treeId)
            ->order(array('level', 'sort_order'));

        $nodes  = array();
        $rowSet = $select->query()->fetchAll();
        foreach ($rowSet as $row) {
            $nodes[intval($row['parent_node_id'])][$row['node_id']] = $row;
        }

        $this->_updateNodeRequestUrls($nodes, 0, $treeRow['identifier']);

        return $this;
    }

    /**
     * Recursive update node Request URLs
     *
     * @param array $nodes
     * @param int $parentNodeId
     * @param string $path
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    protected function _updateNodeRequestUrls(array $nodes, $parentNodeId = 0, $path = '')
    {
        if (!isset($nodes[$parentNodeId])) {
            return $this;
        }
        foreach ($nodes[$parentNodeId] as $nodeRow) {
            $identifier = $nodeRow['page_id'] ? $nodeRow['page_identifier'] : $nodeRow['identifier'];
            $requestUrl = $path . '/' . $identifier;
            if ($nodeRow['request_url'] != $requestUrl) {
                $this->_getWriteAdapter()->update($this->getMainTable(), array(
                    'request_url' => $requestUrl
                ), $this->_getWriteAdapter()->quoteInto('node_id=?', $nodeRow['node_id']));
            }
            if (isset($nodes[$nodeRow['node_id']])) {
                $this->_updateNodeRequestUrls($nodes, $nodeRow['node_id'], $requestUrl);
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
            ->where('page_table.identifier=?', $identifier);
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
     * Load Node by Parent node and Type
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
    public function loadByNodeType($object, $node, $type)
    {
        if (!$object->getParentNode()) {
            return $this;
        }
        $read = $this->_getReadAdapter();
        if ($read) {
            $select = $this->_getLoadSelect('tree_id', $node->getTreeId(), $object);
            $found  = false;
            switch ($type) {
                case 'chapter':
                    $xpath = split('/', $node->getXpath());
                    if (isset($xpath[1]) && $xpath[1] != $node->getId()) {
                        $found = true;
                        $select->where($this->getMainTable() . '.node_id=?', $xpath[1]);
                    }
                    break;

                case 'section':
                    $xpath = split('/', $node->getXpath());
                    if (isset($xpath[2]) && $xpath[2] != $node->getId()) {
                        $found = true;
                        $select->where($this->getMainTable() . '.node_id=?', $xpath[2]);
                    }
                    break;

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
                return $this;
            }

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);
        return $this;
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
                $select = $this->_getLoadSelect('tree_id', $object->getTreeId(), $object)
                    ->where('parent_node_id IN(?)', $parentIds)
                    ->order(array('level', 'sort_order'));
                $tree = $this->_createNodesFromSelect($select, $parentId, $tree);
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

        $select = $this->_getLoadSelect('tree_id', $object->getTreeId(), $object)
            ->where($where)
            ->order(array('level', 'sort_order'));

        $tree = $this->_createNodesFromSelect($select, $parentId, $tree);

        return $tree;
    }

    /**
     * Create Node objects from select
     *
     * @see getTreeSlice
     * @param Varien_Db_Select $select
     * @param int $startNodeId
     * @param array $tree
     * @return array
     */
    protected function _createNodesFromSelect($select, $startNodeId, array $tree = array())
    {
        $nodes = $select->query()->fetchAll();
        foreach ($nodes as $row) {
            $parentNodeId = $row['parent_node_id'] == $startNodeId ? 0 : $row['parent_node_id'];
            $node = Mage::getModel('enterprise_cms/hierarchy_node')
                ->addData($row);
            $tree[$parentNodeId][$node->getId()] = $node;
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
        $select = $this->_getLoadSelect('tree_id', $object->getTreeId(), $object)
            ->where($where)
            ->order('sort_order');
        $nodes = $select->query()->fetchAll();
        foreach ($nodes as $k => $row) {
            $node = Mage::getModel('enterprise_cms/hierarchy_node')
                ->addData($row);
            $children[] = $node;
            unset($nodes[$k]);
        }

        return $children;
    }
}
