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
 * Cms Page Edit Hierarchy Tab Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Hierarchy
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Array of nodes for tree
     * @var array|null
     */
    protected $_nodes = null;

    /**
     * Retrieve current page instance
     *
     * @return Mage_Cms_Model_Page
     */
    public function getPage()
    {
        return Mage::registry('cms_page');
    }

    /**
     * Retrieve Hierarchy JSON string
     *
     * @return string
     */
    public function getNodesJson()
    {
        return Mage::helper('core')->jsonEncode($this->getNodes());
    }

    /**
     * Prepare nodes data from DB
     *
     * @return array
     */
    public function getNodes() {
        if (is_null($this->_nodes)) {
            $collection = Mage::getModel('enterprise_cms/hierarchy_node')->getCollection()
                ->joinCmsPage()
                ->setTreeOrder()
                ->joinPageExistsNodeInfo($this->getPage());

            $this->_nodes = array();

            $_selectedNodes = null;
            if ($this->getPage()->hasData('node_ids')) {
                $_selectedNodes = explode(',', $this->getPage()->getData('node_ids'));
            }

            foreach ($collection as $item) {
                /* @var $item Enterprise_Cms_Model_Hierarchy_Node */
                if (is_array($_selectedNodes)) {
                    if (in_array($item->getId(), $_selectedNodes)) {
                        $item->setPageExists(1);
                    } else {
                        $item->setPageExists(0);
                    }
                }

                $_node = array(
                    'node_id'               => $item->getId(),
                    'parent_node_id'        => $item->getParentNodeId(),
                    'label'                 => $item->getLabel(),
                    'page_exists'           => (bool)$item->getPageExists(),
                    'current_page'          => (bool)$item->getCurrentPage(),
                    'cls'                   => $item->getCurrentPage()?'cur-page':''
                );
                $this->_nodes[] = $_node;
            }
        }
        return $this->_nodes;
    }

    /**
     * Retrieve ids of selected nodes from two sources.
     * First is from prepared data from DB.
     * Second source is data from page model in case we had error.
     *
     * @return string
     */
    public function getSelectedNodeIds()
    {
        if (!$this->getPage()->hasData('node_ids')) {
            $ids = array();

            foreach ($this->getNodes() as $node) {
                if ($node['page_exists']) {
                    $ids[] = $node['node_id'];
                }
            }
            return implode(',', $ids);
        }

        return $this->getPage()->getData('node_ids');
    }

    /**
     * Prepare json string with current page data
     *
     * @return string
     */
    public function getCurrentPageJson()
    {
        $data = array(
            'label' => $this->getPage()->getTitle(),
            'id' => $this->getPage()->getId()
        );

        return Mage::helper('core')->jsonEncode($data);
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('enterprise_cms')->__('Hierarchy');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_cms')->__('Hierarchy');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        if (!$this->getPage()->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
