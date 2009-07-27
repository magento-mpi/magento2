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
 * Cms Hierarchy Edit Pages Tab Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Tab_Pages
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve buttons HTML for Cms Page Grid
     *
     * @return string
     */
    public function getPageGridButtonsHtml()
    {
        $addButtonData = array(
            'id'        => 'add_cms_pages',
            'label'     => Mage::helper('enterprise_cms')->__('Add Selected Page(s) to Tree'),
            'onclick'   => 'hierarchyNodes.pageGridAddSelected()',
            'class'     => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData($addButtonData)->toHtml();
    }

    /**
     * Retrieve Buttons HTML for Page Properties form
     *
     * @return string
     */
    public function getPagePropertiesButtons()
    {
        $buttons = array();
        $buttons[] = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id'        => 'save_node_button',
            'label'     => Mage::helper('enterprise_cms')->__('Save'),
            'onclick'   => 'hierarchyNodes.saveNodePage()',
            'class'     => 'save',
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id'        => 'delete_node_button',
            'label'     => Mage::helper('enterprise_cms')->__('Remove From Tree'),
            'onclick'   => 'hierarchyNodes.deleteNodePage()',
            'class'     => 'delete',
        ))->toHtml();
        $buttons[] = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id'        => 'cancel_node_button',
            'label'     => Mage::helper('enterprise_cms')->__('Cancel'),
            'onclick'   => 'hierarchyNodes.cancelNodePage()',
            'class'     => 'delete',
        ))->toHtml();

        return join(' ', $buttons);
    }

    /**
     * Retrieve buttons HTML for Pages Tree
     *
     * @return string
     */
    public function getTreeButtonsHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'id'        => 'new_node_button',
            'label'     => Mage::helper('enterprise_cms')->__('Add Node ...'),
            'onclick'   => 'hierarchyNodes.newNodePage()',
            'class'     => 'add',
        ))->toHtml();
    }

    /**
     * Retrieve current nodes Json
     *
     * @return string
     */
    public function getNodesJson()
    {
        $nodes = array();
        /* @var $node Enterprise_Cms_Model_Hierarchy_Node */
        $node = Mage::registry('current_hierarchy_node');
        // restore data is exists
        $data = Mage::helper('core')->jsonDecode($node->getNodesData());
        if (is_array($data)) {
            foreach ($data as $v) {
                $nodes[] = array(
                    'node_id'               => $v['node_id'],
                    'parent_node_id'        => $v['parent_node_id'],
                    'label'                 => $v['label'],
                    'identifier'            => $v['identifier'],
                    'page_id'               => empty($v['page_id']) ? null : $v['page_id'],
                );
            }
        } else if ($node->getId()) {
            $collection = $node->getCollection()
                ->addTreeFilter($node->getTreeId())
                ->joinCmsPage()
                ->setTreeOrder();
            foreach ($collection as $item) {
                /* @var $item Enterprise_Cms_Model_Hierarchy_Node */
                $nodes[] = array(
                    'node_id'               => $item->getId(),
                    'parent_node_id'        => $item->getParentNodeId(),
                    'label'                 => $item->getLabel(),
                    'identifier'            => $item->getIdentifier(),
                    'page_id'               => $item->getPageId(),
                );
            }
        }

        return Mage::helper('core')->jsonEncode($nodes);
    }

    /**
     * Retrieve Grid JavaScript object name
     *
     * @return string
     */
    public function getGridJsObject()
    {
        return $this->getChild('cms_page_grid')->getJsObjectName();
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('enterprise_cms')->__('Tree Nodes');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('enterprise_cms')->__('Tree Nodes');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
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
