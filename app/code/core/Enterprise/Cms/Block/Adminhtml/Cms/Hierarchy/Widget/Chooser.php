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
 * Cms Pages Hierarchy Grid Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser extends Mage_Adminhtml_Block_Template
{
    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = $element->getId() . md5(microtime());
        $sourceUrl = $this->getUrl('*/cms_hierarchy_widget/chooser', array('uniq_id' => $uniqId));

        $chooserHtml = $this->getLayout()->createBlock('adminhtml/cms_page_edit_wysiwyg_widget_chooser')
            ->setElement($element)
            ->setSourceUrl($sourceUrl)
            ->toHtml();

        $element->setData('after_element_html', $chooserHtml);
        return $element;
    }

    /**
     * Return JS+HTML to initialize tree
     *
     * @return string
     */
    public function getTreeHtml()
    {
        $html = '
            <div id="tree'.$this->getId().'"></div>
            <script type="text/javascript">

                function clickNode(node) {
                    $("tree-container").insert({before: node.text});
                    $("'.$this->getId().'").value = node.id;
                    treeRoot.collapse();
                }

                tree'.$this->getId().' = new Ext.tree.TreePanel("tree'.$this->getId().'", {
                    animate: false,
                    loader: new Ext.tree.TreeLoader({dataUrl:"'. $this->getTreeLoaderUrl() .'"}),
                    enableDD: true,
                    containerScroll: true,
                    rootVisible: false,
                    lines: true
                });

                treeRoot'.$this->getId().' = new Ext.tree.AsyncTreeNode({
                    text: "'. $this->__("Root") .'",
                    id: "root",
                    allowDrop: true,
                    allowDrag: false,
                    expanded: true,
                    cls: "cms_node_root",
                });
                tree'.$this->getId().'.setRootNode(treeRoot'.$this->getId().');
                tree'.$this->getId().'.addListener("click", function (node, event) {
                    var chooser = $("tree'.$this->getId().'").up().previous("a.widget-option-chooser");

                    var optionLabel = node.text;
                    var optionValue = node.id;

                    chooser.previous("input.widget-option").value = optionValue;
                    chooser.next("label.widget-option-label").update(optionLabel);

                    var responseContainerId = "responseCnt" + chooser.id;
                    $(responseContainerId).hide();
                });
                tree'.$this->getId().'.render();
                treeRoot'.$this->getId().'.expand();
            </script>
        ';
        return $html;
    }

    /**
     * Return Hierarchy Trees or Nodes json
     *
     * @return string
     */
    public function getNodesJson()
    {
        $nodeId = $this->getRequest()->getParam('node');
        if ($nodeId == 'root') {
            return $this->_getTreesJson();
        } else {
            return $this->_getNodesJson($nodeId);
        }
    }

    /**
     * Return Hierarchy Trees json
     *
     * @return string
     */
    protected function _getTreesJson()
    {
        /* @var $collection Enterprise_Cms_Model_Mysql4_Hierarchy_Node_Collection */
        $collection = Mage::getModel('enterprise_cms/hierarchy_node')->getCollection();
        $collection->applyRootNodeFilter()
            ->joinCmsPage();

        $trees = array();
        foreach ($collection as $rootNode) {
            $trees[] = array(
                'id'        => $rootNode->getId(),
                'text'      => $rootNode->getLabel(),
                'cls'       => 'folder',
            );
        }

        return Mage::helper('core')->jsonEncode($trees);
    }

    /**
     * Return Hierarchy Nodes json for parent node
     *
     * @param int $parentNodeId Parent Node Id
     * @return string
     */
    protected function _getNodesJson($parentNodeId)
    {
        $collection = Mage::getModel('enterprise_cms/hierarchy_node')->getCollection()
            ->addFieldToFilter('parent_node_id', $parentNodeId)
            ->joinCmsPage();

        $nodes = array();
        foreach ($collection as $node) {
            /* @var $node Enterprise_Cms_Model_Hierarchy_Node */
            $nodes[] = array(
                'id'    => $node->getNodeId(),
                'text'  => $node->getLabel(),
                'cls'   => 'folder',
            );
        }

        return Mage::helper('core')->jsonEncode($nodes);
    }


    /**
     * Return Hierarchy Tree Source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        return $this->getUrl('*/cms_hierarchy_widget/nodesJson', array('_current' => true));
    }

}
