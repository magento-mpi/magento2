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
 * Cms Hieararchy Pages Node Model
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Hierarchy_Node extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/hierarchy_node');
    }

    /**
     * Retrieve Resource instance
     *
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy_Node
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Load Parent Node by Hierarchy Page Tree ID
     *
     * @param int $treeId
     * @return Enterprise_Cms_Model_Hierarchy_Node
     */
    public function loadByHierarchy($treeId)
    {
        $this->_getResource()->loadByHierarchy($this, $treeId);
        return $this;
    }

    /**
     * Validate Unique Hierarchy Identifier
     *
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function validateHierarchyIdentifier()
    {
        $identifier = $this->getIdentifier();
        if (empty($identifier)) {
            Mage::throwException(Mage::helper('enterprise_cms')->__('Please enter a valid Identifier'));
        }

        if (!$this->_getResource()->validateHierarchyIdentifier($this->getIdentifier(), $this->getTreeId())) {
            Mage::throwException(Mage::helper('enterprise_cms')->__('Hierarchy with same Identifier already exists'));
        }

        return true;
    }

    /**
     * Collect and save tree
     *
     * @param array $data
     * @return Enterprise_Cms_Model_Hierarchy_Node
     */
    public function collectTree($data)
    {
        if (!is_array($data) || !$this->getId()) {
            return $this;
        }

        $nodes = array();
        foreach ($data as $v) {
            $parentNodeId = empty($v['parent_node_id']) ? 0 : $v['parent_node_id'];
            $nodes[$parentNodeId][$v['node_id']] = array(
                'node_id'       => strpos($v['node_id'], '_') === false ? $v['node_id'] : null,
                'page_id'       => empty($v['page_id']) ? null : intval($v['page_id']),
                'tree_id'       => $this->getTreeId(),
                'label'         => !$v['use_def_label'] ? $v['label'] : null,
                'identifier'    => !$v['use_def_identifier'] ? $v['identifier'] : null,
                'level'         => intval($v['level']),
                'sort_order'    => intval($v['sort_order']),
                'request_url'   => $v['identifier']
            );
        }

        $this->_getResource()->removeTreeChilds($this);
        $this->_collectTree($nodes, $this->getId(), $this->getRequestUrl(), 0);

        return $this;
    }

    /**
     * Recursive save nodes
     *
     * @param array $nodes
     * @param int $parentNodeId
     * @param string $path
     * @param int $level
     * @return Enterprise_Cms_Model_Hierarchy_Node
     */
    protected function _collectTree(array $nodes, $parentNodeId, $path = '', $level = 0)
    {
        foreach ($nodes[$level] as $k => $v) {
            $v['parent_node_id'] = $parentNodeId;
            $v['request_url']    = $path . '/' . $v['request_url'];

            // create new node
            $node = Mage::getModel('enterprise_cms/hierarchy_node');
            $node->addData($v)->save();

            if (isset($nodes[$k])) {
                $this->_collectTree($nodes, $node->getId(), $node->getRequestUrl(), $k);
            }
        }
        return $this;
    }

    /**
     * Retrieve Node or Page identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        if (is_null($this->_getData('identifier'))) {
            return $this->_getData('page_identifier');
        }
        return $this->_getData('identifier');
    }

    /**
     * Is Node used original Page Identifier
     *
     * @return bool
     */
    public function isUseDefaultIdentifier()
    {
        return is_null($this->_getData('identifier'));
    }

    /**
     * Retrieve Node label or Page title
     *
     * @return string
     */
    public function getLabel()
    {
        if (is_null($this->_getData('label'))) {
            return $this->_getData('page_title');
        }
        return $this->_getData('label');
    }

    /**
     * Is Node used original Page Label
     *
     * @return bool
     */
    public function isUseDefaultLabel()
    {
        return is_null($this->_getData('label'));
    }
}
