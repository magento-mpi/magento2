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
 * Cms Page Hierarchy Tree Nodes Collection
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Mysql4_Hierarchy_Node_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Define resource model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/hierarchy_node');
    }

    /**
     * Add filter by hierarchy tree
     *
     * @param int $treeId
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node_Collection
     */
    public function addTreeFilter($treeId)
    {
        $this->addFieldToFilter('tree_id', $treeId);
        return $this;
    }

    /**
     * Join Cms Page data to collection
     *
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node_Collection
     */
    public function joinCmsPage()
    {
        $this->getSelect()->joinLeft(
            array('page_table' => $this->getTable('cms/page')),
            'main_table.page_id = page_table.page_id',
            array(
                'page_title'        => 'title',
                'page_identifier'   => 'identifier'
            )
        );
        return $this;
    }

    /**
     * Order nodes as tree
     *
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node_Collection
     */
    public function setTreeOrder()
    {
        $this->getSelect()->where('main_table.parent_node_id IS NOT NULL');
        $this->getSelect()->order(array(
            'level', 'main_table.sort_order'
        ));
        return $this;
    }
}
