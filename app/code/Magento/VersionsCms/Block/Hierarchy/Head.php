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
namespace Magento\VersionsCms\Block\Hierarchy;

class Head extends \Magento\Core\Block\AbstractBlock
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @var \Magento\VersionsCms\Helper\Hierarchy|null
     */
    protected $_cmsHierarchy = null;

    /**
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\Core\Block\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
        \Magento\Core\Block\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
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
        /* @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
        $node = $this->_coreRegistry->registry('current_cms_hierarchy_node');
        /* @var $head Magento_Page_Block_Html_Head */
        $head = $this->getLayout()->getBlock('head');

        if ($this->_cmsHierarchy->isMetadataEnabled() && $node && $head) {
            $treeMetaData = $node->getTreeMetaData();
            if (is_array($treeMetaData)) {
                /* @var $linkNode \Magento\VersionsCms\Model\Hierarchy\Node */

                if ($treeMetaData['meta_cs_enabled']) {
                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_CHAPTER
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_CHAPTER, $linkNode->getUrl()
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_SECTION
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_SECTION, $linkNode->getUrl()
                        );
                    }
                }

                if ($treeMetaData['meta_next_previous']) {
                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_NEXT
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_NEXT, $linkNode->getUrl()
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_PREVIOUS
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_PREVIOUS, $linkNode->getUrl()
                        );
                    }
                }

                if ($treeMetaData['meta_first_last']) {
                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_FIRST
                    );
                    if ($linkNode->getId()) {
                        $head->addLinkRel(
                            \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_FIRST, $linkNode->getUrl()
                        );
                    }
                }
            }
        }

        return parent::_prepareLayout();
    }
}
