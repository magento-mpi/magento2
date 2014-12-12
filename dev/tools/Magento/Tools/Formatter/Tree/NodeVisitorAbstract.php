<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\Tree;

/**
 * This class is a base class for visitors so that all methods do not need to be implemented.
 *
 * Class NodeVisitorAbstract
 */
class NodeVisitorAbstract implements NodeVisitorInterface
{
    /**
     * This method is called when first visiting a node.
     *
     * @param TreeNode $treeNode
     * @return void
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        // by default, do nothing
    }

    /**
     * This method is called when exiting a visited node.
     *
     * @param TreeNode $treeNode
     * @return void
     */
    public function nodeExit(TreeNode $treeNode)
    {
        // by default, do nothing
    }
}
