<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms Pages Hierarchy Grid Block
 *
 * @method Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser setScope(string $value)
 * @method Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser setScopeId(int $value)
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Widget_Chooser extends Magento_Adminhtml_Block_Template
{
    /**
     * @var Magento_VersionsCms_Model_Hierarchy_NodeFactory
     */
    protected $_nodeFactory;

    /**
     * @var Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Widget_Radio
     */
    protected $_widgetRadio;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_VersionsCms_Model_Hierarchy_NodeFactory $nodeFactory
     * @param Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Widget_Radio $widgetRadio
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_VersionsCms_Model_Hierarchy_NodeFactory $nodeFactory,
        Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Widget_Radio $widgetRadio,
        array $data = array()
    ) {
        $this->_nodeFactory = $nodeFactory;
        $this->_widgetRadio = $widgetRadio;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Magento_Data_Form_Element_Abstract $element Form Element
     * @return Magento_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $uniqueId = $this->_coreData->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/cms_hierarchy_widget/chooser', array('uniq_id' => $uniqueId));

        $chooser = $this->getLayout()->createBlock('Magento_Widget_Block_Adminhtml_Widget_Chooser')
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqueId);


        if ($element->getValue()) {
            $node = $this->_nodeFactory->create()->load($element->getValue());
            if ($node->getId()) {
                $chooser->setLabel($node->getLabel());
            }
        }

        $radioHtml = $this->_widgetRadio->setUniqId($uniqueId)->toHtml();

        $element->setData('after_element_html', $chooser->toHtml() . $radioHtml);

        return $element;
    }

    /**
     * Return JS+HTML to initialize tree
     *
     * @return string
     */
    public function getTreeHtml()
    {
        $chooserJsObject = $this->getId();
        $html = '
            <div id="tree' . $this->getId() . '" class="cms-tree tree x-tree"></div>
            <script type="text/javascript">

            function clickNode(node) {
                $("tree-container").insert({before: node.text});
                $("' . $this->getId() . '").value = node.id;
                treeRoot.collapse();
            }

            var nodes = ' . $this->getNodesJson() . ';

            if (nodes.length > 0) {
                var tree' . $this->getId() . ' = new Ext.tree.TreePanel("tree' . $this->getId() . '", {
                    animate: false,
                    enableDD: false,
                    containerScroll: true,
                    rootVisible: false,
                    lines: true
                });

                var treeRoot' . $this->getId() . ' = new Ext.tree.AsyncTreeNode({
                    text: "' . __("Root") . '",
                    id: "root",
                    allowDrop: true,
                    allowDrag: false,
                    expanded: true,
                    cls: "cms_node_root"
                });

                tree' . $this->getId() . '.setRootNode(treeRoot' . $this->getId() . ');

                for (var i = 0; i < nodes.length; i++) {
                    var cls = nodes[i].page_id ? "cms_page" : "cms_node";
                    var node = new Ext.tree.TreeNode({
                        id: nodes[i].node_id,
                        text: nodes[i].label,
                        cls: cls,
                        expanded: nodes[i].page_exists,
                        allowDrop: false,
                        allowDrag: false,
                        page_id: nodes[i].page_id
                    });
                    if (parentNode = tree' . $this->getId() . '.getNodeById(nodes[i].parent_node_id)) {
                        parentNode.appendChild(node);
                    } else {
                        treeRoot' . $this->getId() . '.appendChild(node);
                    }
                }

                tree' . $this->getId() . '.addListener("click", function (node, event) {
                    ' . $chooserJsObject . '.setElementValue(node.id);
                    ' . $chooserJsObject . '.setElementLabel(node.text);
                    ' . $chooserJsObject . '.close();
                });
                tree' . $this->getId() . '.render();
                treeRoot' . $this->getId() . '.expand();
            }
            else {
                $("tree' . $this->getId() . '").innerHTML = "' . __('No nodes are available.') . '";
            }
            </script>
        ';
        return $html;
    }

    /**
     * Retrieve Hierarchy JSON string
     *
     * @return string
     */
    public function getNodesJson()
    {
        return $this->_coreData->jsonEncode($this->getNodes());
    }

    /**
     * Prepare hierarchy nodes for tree building
     *
     * @return array
     */
    public function getNodes()
    {
        /** @var $hierarchyNode Magento_VersionsCms_Model_Hierarchy_Node */
        $hierarchyNode = $this->_nodeFactory->create();
        $hierarchyNode->setScope($this->getScope());
        $hierarchyNode->setScopeId($this->getScopeId());

        $nodeHeritage = $hierarchyNode->getHeritage();
        unset($hierarchyNode);
        return $nodeHeritage->getNodesData();
    }
}
