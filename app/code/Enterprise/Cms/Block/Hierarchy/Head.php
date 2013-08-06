<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Hierarchy Head Block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Block_Hierarchy_Head extends Mage_Core_Block_Abstract
{
    /**
     * Prepare Global Layout
     *
     * @return Enterprise_Cms_Block_Hieararchy_Head
     */
    protected function _prepareLayout()
    {
        /* @var $node Enterprise_Cms_Model_Hierarchy_Node */
        $node      = Mage::registry('current_cms_hierarchy_node');
        /* @var $head Mage_Page_Block_Html_Head */
        $head      = $this->getLayout()->getBlock('head');

        if (Mage::helper('Enterprise_Cms_Helper_Hierarchy')->isMetadataEnabled() && $node && $head) {
            $treeMetaData = $node->getTreeMetaData();
            if (is_array($treeMetaData)) {
                /* @var $linkNode Enterprise_Cms_Model_Hierarchy_Node */

                if ($treeMetaData['meta_cs_enabled']) {
                    $linkNode = $node->getMetaNodeByType(Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_CHAPTER);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'mage-page-head-chapter-link',
                            'Mage_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_CHAPTER
                                ))
                            )
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_SECTION);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'mage-page-head-section-link',
                            'Mage_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_SECTION
                                ))
                            )
                        );
                    }
                }

                if ($treeMetaData['meta_next_previous']) {
                    $linkNode = $node->getMetaNodeByType(Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_NEXT);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'mage-page-head-next-link',
                            'Mage_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_NEXT
                                ))
                            )
                        );
                    }

                    $linkNode = $node->getMetaNodeByType(Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_PREVIOUS);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'mage-page-head-previous-link',
                            'Mage_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_PREVIOUS
                                ))
                            )
                        );
                    }
                }

                if ($treeMetaData['meta_first_last']) {
                    $linkNode = $node->getMetaNodeByType(Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_FIRST);
                    if ($linkNode->getId()) {
                        $head->addChild(
                            'mage-page-head-first-link',
                            'Mage_Page_Block_Html_Head_Link',
                            array(
                                'url' => $linkNode->getUrl(),
                                'properties' => array('attributes' => array(
                                    'rel' => Enterprise_Cms_Model_Hierarchy_Node::META_NODE_TYPE_FIRST
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
