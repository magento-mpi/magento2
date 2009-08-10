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
 * Cms Pages Hierarchy Tree Collection
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Mysql4_Hierarchy_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Define resource model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/hierarchy');
    }

    /**
     * Add Pages Number for Page Tree
     *
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Collection
     */
    public function joinPagesCount()
    {
        $this->getSelect()->join(
            array('node_count_table' => $this->getTable('enterprise_cms/hierarchy_node')),
            'main_table.tree_id=node_count_table.tree_id AND node_count_table.page_id IS NOT NULL',
            array('pages_count' => 'COUNT(node_count_table.node_id)')
        );
        $this->getSelect()->group('main_table.tree_id');

        return $this;
    }

    /**
     * Add used page statistic
     *
     * @param Mage_Cms_Model_Page|int $page
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Collection
     */
    public function addContainPageFilter($page)
    {
        if ($page instanceof Mage_Cms_Model_Page) {
            $page = $page->getId();
        }

        $this->getSelect()->join(
            array('node_page_filter_table' => $this->getTable('enterprise_cms/hierarchy_node')),
            'main_table.tree_id=node_page_filter_table.tree_id AND '
                . $this->getConnection()->quoteInto('node_page_filter_table.page_id=?', $page),
            array()
        )->group('node_page_filter_table.page_id');

        return $this;
    }

    /**
     * Add mapped field name for filter to collection
     *
     * @param string $field
     * @param string $mappedField
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Collection
     */
    protected function _addFieldToMap($field, $mappedField)
    {
        if (!is_array($this->_map)) {
            $this->_map = array();
        }
        if (empty($this->_map['fields'])) {
            $this->_map['fields'] = array();
        }

        $this->_map['fields'][$field] = $mappedField;
    }

    /**
     * Join root node to hierarchy collection
     *
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Collection
     */
    public function joinRootNode()
    {
        $this->getSelect()->joinLeft(
            array('node_table' => $this->getTable('enterprise_cms/hierarchy_node')),
            'main_table.tree_id=node_table.tree_id AND node_table.parent_node_id IS NULL',
            array('node_id','label','identifier','page_id')
        );

        $this->getSelect()->joinLeft(
            array('page_table' => $this->getTable('cms/page')),
            'node_table.page_id=page_table.page_id',
            array(
                'page_title'        => 'title',
                'page_identifier'   => 'identifier',
                'page_is_active'    => 'is_active'
            )
        );

        if (!is_array($this->_map)) {
            $this->_map = array();
        }
        if (empty($this->_map['fields'])) {
            $this->_map['fields'] = array();
        }

        $this->_addFieldToMap('identifier', 'node_table.identifier');
        $this->_addFieldToMap('label', 'node_table.label');
        $this->_addFieldToMap('page_id', 'page_table.page_id');
        $this->_addFieldToMap('page_title', 'page_table.title');
        $this->_addFieldToMap('page_identifier', 'page_table.identifier');
        $this->_addFieldToMap('page_is_active', 'page_table.is_active');

        return $this;
    }

    /**
     * Add field filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @see self::_getConditionSql for $condition
     * @param string|array $attribute
     * @param null|string|array $condition
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'identifier') {
            $where = join(' OR ', array(
                $this->_getConditionSql($this->_getMappedField('identifier'), $condition),
                $this->_getConditionSql($this->_getMappedField('page_identifier'), $condition),
            ));
            $this->getSelect()->where($where);

            return $this;
        } else if ($field == 'label') {
            $where = join(' OR ', array(
                $this->_getConditionSql($this->_getMappedField('label'), $condition),
                $this->_getConditionSql($this->_getMappedField('page_title'), $condition),
            ));
            $this->getSelect()->where($where);

            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
