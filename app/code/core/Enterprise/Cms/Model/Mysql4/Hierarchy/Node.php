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
                'page_identifier'   => 'identifier'
            )
        );
        return $select;
    }

    /**
     * Load Parent Node by Hierarchy Page Tree ID
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $object
     * @param int $treeId
     * @return Enterprise_Cms_Model_Hierarchy_Node
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
}
