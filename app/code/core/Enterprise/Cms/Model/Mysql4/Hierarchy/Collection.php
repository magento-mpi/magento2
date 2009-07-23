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
        return $this;
    }
}
