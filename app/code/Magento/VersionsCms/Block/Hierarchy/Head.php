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
 * Cms Hierarchy Head Block
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Block_Hierarchy_Head extends Magento_Core_Block_Abstract
{
    /**
     * @var Magento_VersionsCms_Helper_Hierarchy|null
     */
    protected $_cmsHierarchy = null;

    /**
     * @param Magento_VersionsCms_Helper_Hierarchy $cmsHierarchy
     * @param Magento_Core_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_VersionsCms_Helper_Hierarchy $cmsHierarchy,
        Magento_Core_Block_Context $context,
        array $data = array()
    ) {
        $this->_cmsHierarchy = $cmsHierarchy;
        parent::__construct($context, $data);
    }
    /**
     * Prepare Global Layout
     *
     * @return Magento_VersionsCms_Block_Hieararchy_Head
     */
    protected function _prepareLayout()
    {
        /* @var $node Magento_VersionsCms_Model_Hierarchy_Node */
        $node = Mage::registry('current_cms_hierarchy_node');
        /* @var $head Magento_Page_Block_Html_Head */
        $head = $this->getLayout()->getBlock('head');

        if ($this->_cmsHierarchy->isMetadataEnabled() && $node && $head) {
            $treeMetaData = $node->getTreeMetaData();
            if (is_array($treeMetaData)) {
                /* @var $linkNode Magento_VersionsCms_Model_Hierarchy_Node */

                if ($treeMetaData['meta_cs_enabled']) {
                    $linkNode = $node->getMetaNodeByType(
                        Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_CHAPTER
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_CHAPTER, $linkNode->getUrl()
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(
                        Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_SECTION
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_SECTION, $linkNode->getUrl()
                        );
                    }
                }

                if ($treeMetaData['meta_next_previous']) {
                    $linkNode = $node->getMetaNodeByType(
                        Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_NEXT
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_NEXT, $linkNode->getUrl()
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(
                        Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_PREVIOUS
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_PREVIOUS, $linkNode->getUrl()
                        );
                    }
                }

                if ($treeMetaData['meta_first_last']) {
                    $linkNode = $node->getMetaNodeByType(
                        Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_FIRST
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_FIRST, $linkNode->getUrl()
                        );
                    }
                }
            }
        }

        return parent::_prepareLayout();
    }
}
