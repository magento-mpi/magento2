<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Block\Hierarchy;

/**
 * Cms Hierarchy Head Block
 *
 */
class Head extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\VersionsCms\Helper\Hierarchy|null
     */
    protected $_cmsHierarchy;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Page\Config $pageConfig,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_cmsHierarchy = $cmsHierarchy;
        $this->pageConfig = $pageConfig;
        parent::__construct($context, $data);
    }

    /**
     * Prepare Global Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /* @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
        $node = $this->_coreRegistry->registry('current_cms_hierarchy_node');
        if ($this->_cmsHierarchy->isMetadataEnabled() && $node) {
            $treeMetaData = $node->getTreeMetaData();
            if (is_array($treeMetaData)) {
                /* @var $linkNode \Magento\VersionsCms\Model\Hierarchy\Node */

                if ($treeMetaData['meta_cs_enabled']) {
                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_CHAPTER
                    );
                    if ($linkNode->getId()) {
                        $this->pageConfig->addRemotePageAsset(
                            $linkNode->getUrl(),
                            [
                                'attributes' => [
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_CHAPTER,
                                ]
                            ]
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_SECTION
                    );
                    if ($linkNode->getId()) {
                        $this->pageConfig->addRemotePageAsset(
                            $linkNode->getUrl(),
                            [
                                'attributes' => [
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_SECTION,
                                ]
                            ]
                        );
                    }
                }

                if ($treeMetaData['meta_next_previous']) {
                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_NEXT
                    );
                    if ($linkNode->getId()) {
                        $this->pageConfig->addRemotePageAsset(
                            $linkNode->getUrl(),
                            [
                                'attributes' => [
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_NEXT,
                                ]
                            ]
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_PREVIOUS
                    );
                    if ($linkNode->getId()) {
                        $this->pageConfig->addRemotePageAsset(
                            $linkNode->getUrl(),
                            [
                                'attributes' => [
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_PREVIOUS,
                                ]
                            ]
                        );
                    }
                }

                if ($treeMetaData['meta_first_last']) {
                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_FIRST
                    );
                    if ($linkNode->getId()) {
                        $this->pageConfig->addRemotePageAsset(
                            $linkNode->getUrl(),
                            [
                                'attributes' => [
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_FIRST,
                                ]
                            ]
                        );
                    }
                }
            }
        }

        return parent::_prepareLayout();
    }
}
