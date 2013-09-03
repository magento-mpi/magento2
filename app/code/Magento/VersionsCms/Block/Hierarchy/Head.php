<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Hierarchy Head Block
 *
 * @category   Enterprise
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Block_Hierarchy_Head extends Magento_Core_Block_Abstract
{
    /**
     * Prepare Global Layout
     *
     * @return Magento_VersionsCms_Block_Hieararchy_Head
     */
    protected function _prepareLayout()
    {
        /* @var $node Magento_VersionsCms_Model_Hierarchy_Node */
        $node      = Mage::registry('current_cms_hierarchy_node');
        /* @var $head Magento_Page_Block_Html_Head */
        $head      = $this->getLayout()->getBlock('head');

        if (Mage::helper('Magento_VersionsCms_Helper_Hierarchy')->isMetadataEnabled() && $node && $head) {
            $treeMetaData = $node->getTreeMetaData();
            if (is_array($treeMetaData)) {
                /* @var $linkNode Magento_VersionsCms_Model_Hierarchy_Node */

                if ($treeMetaData['meta_cs_enabled']) {
                    $linkNode = $node->getMetaNodeByType(Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_CHAPTER);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'magento-page-head-chapter-link',
                            'Magento_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_CHAPTER
                                ))
                            )
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_SECTION);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'magento-page-head-section-link',
                            'Magento_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_SECTION
                                ))
                            )
                        );
                    }
                }

                if ($treeMetaData['meta_next_previous']) {
                    $linkNode = $node->getMetaNodeByType(Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_NEXT);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'magento-page-head-next-link',
                            'Magento_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_NEXT
                                ))
                            )
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_PREVIOUS);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'magento-page-head-previous-link',
                            'Magento_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_PREVIOUS
                                ))
                            )
                        );
                    }
                }

                if ($treeMetaData['meta_first_last']) {
                    $linkNode = $node->getMetaNodeByType(Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_FIRST);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'magento-page-head-first-link',
                            'Magento_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Magento_VersionsCms_Model_Hierarchy_Node::META_NODE_TYPE_FIRST
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
