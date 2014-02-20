<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Hierarchy;

/**
 * Cms Hierarchy Head Block
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Head extends \Magento\View\Element\AbstractBlock
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
     * @param \Magento\View\Element\Context $context
     * @param \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        \Magento\VersionsCms\Helper\Hierarchy $cmsHierarchy,
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
     * @return $this
     */
    protected function _prepareLayout()
    {
        /* @var $node \Magento\VersionsCms\Model\Hierarchy\Node */
        $node = $this->_coreRegistry->registry('current_cms_hierarchy_node');
        /* @var $head \Magento\Theme\Block\Html\Head */
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
                        $head->addChild(
                            'magento-page-head-chapter-link',
                            'Magento\Theme\Block\Html\Head\Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_CHAPTER
                                ))
                            )
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_SECTION
                    );
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'magento-page-head-section-link',
                            'Magento\Theme\Block\Html\Head\Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_SECTION
                                ))
                            )
                        );
                    }
                }

                if ($treeMetaData['meta_next_previous']) {
                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_NEXT
                    );
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'magento-page-head-next-link',
                            'Magento\Theme\Block\Html\Head\Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_NEXT
                                ))
                            )
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_PREVIOUS
                    );
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'magento-page-head-previous-link',
                            'Magento\Theme\Block\Html\Head\Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_PREVIOUS
                                ))
                            )
                        );
                    }
                }

                if ($treeMetaData['meta_first_last']) {
                    $linkNode = $node->getMetaNodeByType(
                        \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_FIRST
                    );
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'magento-page-head-first-link',
                            'Magento\Theme\Block\Html\Head\Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => \Magento\VersionsCms\Model\Hierarchy\Node::META_NODE_TYPE_FIRST
                                ))
                            )
                        );
                    }
                }
            }
        }

        return parent::_prepareLayout();
    }
}
