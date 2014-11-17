<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget;

/**
 * Cms Pages Hierarchy Grid Block
 *
 * @method \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Chooser setScope(string $value)
 * @method \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Chooser setScopeId(int $value)
 */
class Chooser extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory
     */
    protected $_nodeFactory;

    /**
     * @var \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Radio
     */
    protected $_widgetRadio;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\VersionsCms\Model\Hierarchy\NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\VersionsCms\Model\Hierarchy\NodeFactory $nodeFactory,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_nodeFactory = $nodeFactory;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_widgetRadio = $this->getLayout()
            ->createBlock('Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Radio');
        return parent::_prepareLayout();
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $uniqueId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('adminhtml/cms_hierarchy_widget/chooser', array('uniq_id' => $uniqueId));

        $chooser = $this->getLayout()->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Chooser'
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqueId
        );


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
            <div id="tree' .
            $this->getId() .
            '" class="cms-tree tree x-tree"></div>
            <script type="text/javascript">

            function clickNode(node) {
                $("tree-container").insert({before: node.text});
                $("' .
            $this->getId() .
            '").value = node.id;
                treeRoot.collapse();
            }

            var nodes = ' .
            $this->getNodesJson() .
            ';

            if (nodes.length > 0) {
                var tree' .
            $this->getId() .
            ' = new Ext.tree.TreePanel("tree' .
            $this->getId() .
            '", {
                    animate: false,
                    enableDD: false,
                    containerScroll: true,
                    rootVisible: false,
                    lines: true
                });

                var treeRoot' .
            $this->getId() .
            ' = new Ext.tree.AsyncTreeNode({
                    text: "' .
            __(
                "Root"
            ) .
            '",
                    id: "root",
                    allowDrop: true,
                    allowDrag: false,
                    expanded: true,
                    cls: "cms_node_root"
                });

                tree' .
            $this->getId() .
            '.setRootNode(treeRoot' .
            $this->getId() .
            ');

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
                    if (parentNode = tree' .
            $this->getId() .
            '.getNodeById(nodes[i].parent_node_id)) {
                        parentNode.appendChild(node);
                    } else {
                        treeRoot' .
            $this->getId() .
            '.appendChild(node);
                    }
                }

                tree' .
            $this->getId() .
            '.addListener("click", function (node, event) {
                    ' .
            $chooserJsObject .
            '.setElementValue(node.id);
                    ' .
            $chooserJsObject .
            '.setElementLabel(node.text);
                    ' .
            $chooserJsObject .
            '.close();
                });
                tree' .
            $this->getId() .
            '.render();
                treeRoot' .
            $this->getId() .
            '.expand();
            }
            else {
                $("tree' .
            $this->getId() .
            '").innerHTML = "' .
            __(
                'No nodes are available.'
            ) . '";
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
        return $this->_jsonEncoder->encode($this->getNodes());
    }

    /**
     * Prepare hierarchy nodes for tree building
     *
     * @return array
     */
    public function getNodes()
    {
        /** @var $hierarchyNode \Magento\VersionsCms\Model\Hierarchy\Node */
        $hierarchyNode = $this->_nodeFactory->create();
        $hierarchyNode->setScope($this->getScope());
        $hierarchyNode->setScopeId($this->getScopeId());

        $nodeHeritage = $hierarchyNode->getHeritage();
        unset($hierarchyNode);
        return $nodeHeritage->getNodesData();
    }
}
